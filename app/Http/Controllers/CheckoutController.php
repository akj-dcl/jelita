<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $cart = Cart::where('user_id', $userId)->first();

        $items = [];
        if ($cart) {
            $items = $cart->items()->with(['product.images', 'product.category', 'variant'])->get()->map(function ($item) {
                $name = $item->product->name;
                $price = $item->product->price;
                $weight = $item->product->weight ?? 500;

                if ($item->variant) {
                    $name .= ' - ' . $item->variant->name;
                    $price = $item->variant->price;
                    if ($item->variant->weight) {
                        $weight = $item->variant->weight;
                    }
                }

                return [
                    'id'         => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name'       => $name,
                    'category'   => $item->product->category ? $item->product->category->name : 'Uncategorized',
                    'price'      => $price,
                    'quantity'   => $item->quantity,
                    'weight'     => $weight,
                    'image'      => $item->product->images->first()
                        ? asset('storage/' . $item->product->images->first()->image_path)
                        : 'https://placehold.co/400',
                ];
            });
        }

        $savedAddresses = Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return inertia('Checkout', [
            'cartItems'      => $items,
            'savedAddresses' => $savedAddresses,
            'user'           => [
                'email' => auth()->user()->email,
                'name'  => auth()->user()->name,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $isCod = $request->payment_method === 'cod' || $request->courier === 'cod';

        $request->validate([
            'email'          => 'required|email',
            'firstName'      => 'required|string',
            'lastName'       => 'nullable|string',
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'destination_id' => $isCod ? 'nullable' : 'required',
            'destination_label' => 'nullable|string',
            'courier'        => 'required|string',

            'subtotal'       => 'required|numeric',
            'shippingCost'   => 'required|numeric',
            'tax'            => 'required|numeric',
            'total'          => 'required|numeric',

            'payment_method' => 'required|in:bank_transfer,qris,cod',
            'payment_proof'  => $isCod ? 'nullable|image|max:5120' : 'required|image|max:5120',
        ]);

        $userId = auth()->id();
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json(['error' => 'Keranjang belanja Anda kosong.'], 400);
        }

        $cartItems = $cart->items()->with(['product', 'variant'])->get();

        // Validasi ketersediaan stok sebelum checkout
        foreach ($cartItems as $item) {
            $availableStock = $item->variant_id && $item->variant
                ? $item->variant->stock
                : ($item->product ? $item->product->stock : 0);

            if ($item->quantity > $availableStock) {
                $name = $item->product ? $item->product->name : 'Produk';
                if ($item->variant) {
                    $name .= ' - ' . $item->variant->name;
                }
                return response()->json([
                    'error' => "Stok untuk '{$name}' tidak mencukupi (tersisa {$availableStock}). Silakan sesuaikan jumlah di keranjang."
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id'         => $userId,
                'order_number'    => 'ORD-' . strtoupper(Str::random(10)),
                'status'          => $isCod ? 'processing' : 'pending_payment',
                'subtotal'        => $request->subtotal,
                'shipping_cost'   => $request->shippingCost,
                'tax'             => $request->tax,
                'discount'        => 0,
                'total'           => $request->total,
                'shipping_method' => $request->courier,
                'ship_name'       => trim($request->firstName . ' ' . $request->lastName),
                'ship_phone'      => $request->phone,
                'ship_address'    => $request->address,
                'ship_city'       => $request->destination_label ?? ($request->destination_id ?? 'COD'),
                'ship_province'   => '',
                'ship_postal_code'=> $request->postalCode ?? '',
                'notes'           => $isCod ? 'Metode Pembayaran: COD (Bayar di Tempat)' : null,
            ]);

            // Save cart items as order items with variant name, price, and decrement stock
            foreach ($cartItems as $item) {
                $productName = $item->product ? $item->product->name : 'Produk';
                $price = $item->product ? $item->product->price : 0;

                if ($item->variant) {
                    $productName .= ' - ' . $item->variant->name;
                    $price = $item->variant->price;
                }

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'variant_id'    => $item->variant_id,
                    'product_name'  => $productName,
                    'product_price' => $price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $price * $item->quantity,
                ]);

                // Kurangi stok varian jika ada
                if ($item->variant_id && $item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                }

                // Kurangi stok produk utama
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            // Clear the cart after order is placed
            $cart->items()->delete();

            // Handle payment proof upload
            $path = null;
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('payments', 'public');
            }

            // Create Payment (map to actual payments columns)
            Payment::create([
                'order_id'       => $order->id,
                'method'         => $request->payment_method,
                'amount'         => $request->total,
                'status'         => 'pending',
                'transfer_proof' => $path,
            ]);

            DB::commit();

            return response()->json([
                'message'      => 'Order placed successfully',
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return response()->json(['error' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }
}

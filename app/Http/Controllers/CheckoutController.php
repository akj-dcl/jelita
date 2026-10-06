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
            $items = $cart->items()->with(['product.images'])->get()->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'product_id' => $item->product_id,
                    'name'       => $item->product->name,
                    'category'   => $item->product->category ? $item->product->category->name : 'Uncategorized',
                    'price'      => $item->product->price,
                    'quantity'   => $item->quantity,
                    'weight'     => $item->product->weight ?? 500,
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
        $request->validate([
            'email'          => 'required|email',
            'firstName'      => 'required|string',
            'lastName'       => 'nullable|string',
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'destination_id' => 'required',
            'destination_label' => 'nullable|string',
            'courier'        => 'required|string',

            'subtotal'       => 'required|numeric',
            'shippingCost'   => 'required|numeric',
            'tax'            => 'required|numeric',
            'total'          => 'required|numeric',

            'payment_method' => 'required|in:bank_transfer,qris',
            'payment_proof'  => 'required|image|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();

            // Create Order
            $order = Order::create([
                'user_id'         => $userId,
                'order_number'    => 'ORD-' . strtoupper(Str::random(10)),
                'status'          => 'pending_payment',
                'subtotal'        => $request->subtotal,
                'shipping_cost'   => $request->shippingCost,
                'tax'             => $request->tax,
                'discount'        => 0,
                'total'           => $request->total,
                'shipping_method' => $request->courier,
                'ship_name'       => trim($request->firstName . ' ' . $request->lastName),
                'ship_phone'      => $request->phone,
                'ship_address'    => $request->address,
                'ship_city'       => $request->destination_label ?? $request->destination_id,
                'ship_province'   => '',
                'ship_postal_code'=> $request->postalCode ?? '',
                'notes'           => null,
            ]);

            // Save cart items as order items
            $cart = Cart::where('user_id', $userId)->first();
            if ($cart) {
                foreach ($cart->items()->with('product')->get() as $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'product_price' => $item->product->price,
                        'quantity'      => $item->quantity,
                        'subtotal'      => $item->product->price * $item->quantity,
                    ]);
                }
                // Clear the cart after order is placed
                $cart->items()->delete();
            }

            // Handle payment proof upload
            $path = $request->file('payment_proof')->store('payments', 'public');

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

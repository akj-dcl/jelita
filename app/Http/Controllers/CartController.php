<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private function getCart()
    {
        $sessionId = Session::getId();
        $userId = auth()->check() ? auth()->id() : null;

        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId], ['session_id' => $sessionId]);
            $sessionCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            if ($sessionCart && $sessionCart->id !== $cart->id) {
                foreach ($sessionCart->items as $item) {
                    $existingItem = $cart->items()
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->first();
                    if ($existingItem) {
                        $existingItem->quantity += $item->quantity;
                        $existingItem->save();
                    } else {
                        $item->cart_id = $cart->id;
                        $item->save();
                    }
                }
                $sessionCart->delete();
            }
        } else {
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        return $cart;
    }

    public function index()
    {
        $cart = $this->getCart();
        $items = $cart->items()->with(['product.images', 'variant'])->get()->map(function ($item) {
            $productName = $item->product->name;
            $price = $item->product->price;
            
            if ($item->variant) {
                $productName .= ' - ' . $item->variant->name;
                $price = $item->variant->price;
            }
            
            return [
                'id' => $item->id, // Cart item ID
                'product_id' => $item->product_id,
                'name' => $productName,
                'category' => $item->product->category ? $item->product->category->name : 'Uncategorized',
                'price' => $price,
                'quantity' => $item->quantity,
                'image' => $item->product->images->first() ? asset('storage/' . $item->product->images->first()->image_path) : 'https://placehold.co/400',
            ];
        });

        $recommendedProducts = Product::with('images')->inRandomOrder()->take(4)->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : 'https://placehold.co/400',
            ];
        });

        return inertia('Cart', [
            'cartItems' => $items,
            'recommendedProducts' => $recommendedProducts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $cartItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item added to cart.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $cartItem = $cart->items()->where('id', $id)->firstOrFail();
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $id)->delete();

        return redirect()->back();
    }
}

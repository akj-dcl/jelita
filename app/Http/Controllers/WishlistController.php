<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display a listing of the user's wishlist.
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product', 'product.images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($wishlist) {
                return $wishlist->product;
            });

        return Inertia::render('Wishlist', [
            'wishlistItems' => $wishlists
        ]);
    }

    /**
     * Toggle a product in the user's wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $action = 'removed';
            $isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $action = 'added';
            $isWishlisted = true;
        }

        return back();
    }
}

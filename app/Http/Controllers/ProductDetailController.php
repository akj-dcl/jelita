<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductDetailController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['category', 'images', 'reviews.user', 'variants'])->findOrFail($id);
        $product->append(['average_rating', 'reviews_count']);
        $product->increment('view_count');
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_published', true)
            ->take(4)
            ->get();

        $isWishlisted = false;
        $wishlistCount = 0;
        
        if (\Illuminate\Support\Facades\Auth::check()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $isWishlisted = \App\Models\Wishlist::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->exists();
            $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
        }

        return Inertia::render('ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'isWishlisted' => $isWishlisted,
            'wishlistCount' => $wishlistCount
        ]);
    }
}

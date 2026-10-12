<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $products   = Product::where('is_published', true)->with('category')->latest()->take(4)->get();
        
        $reviews = Review::with(['user', 'product'])
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'comment'      => $r->comment,
                'user_name'    => $r->user ? $r->user->name : 'Pelanggan',
                'product_name' => $r->product ? $r->product->name : '',
                'created_at'   => $r->created_at?->diffForHumans() ?? '',
            ]);

        return Inertia::render('Welcome', [
            'categories' => $categories,
            'products'   => $products,
            'reviews'    => $reviews,
        ]);
    }
}


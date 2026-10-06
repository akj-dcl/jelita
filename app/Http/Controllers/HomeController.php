<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::where('is_active', true)->get();
        $products = \App\Models\Product::where('is_published', true)->with('category')->latest()->take(4)->get();
        
        return \Inertia\Inertia::render('Welcome', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}

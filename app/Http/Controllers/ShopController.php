<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();
        
        $query = \App\Models\Product::where('is_published', true)->with('category')->latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($request->filled('category')) {
            $category = \App\Models\Category::where('slug', $request->category)->orWhere('id', $request->category)->first();
            if ($category) {
                $categoryIds = $category->children()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }
        
        if ($request->filled('maxPrice')) {
            $query->where('price', '<=', $request->maxPrice);
        }

        $products = $query->paginate(12)->withQueryString();
        
        return \Inertia\Inertia::render('Shop', [
            'categories' => $categories,
            'products' => $products,
            'filters' => $request->only(['search', 'category', 'maxPrice', 'minRating'])
        ]);
    }
}

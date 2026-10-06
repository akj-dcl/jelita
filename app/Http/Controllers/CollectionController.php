<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CollectionController extends Controller
{
    public function index()
    {
        // Get only parent categories for the collections page
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) {
                // Count products in this category AND all its children
                $childIds = $category->children->pluck('id')->toArray();
                $allCategoryIds = array_merge([$category->id], $childIds);
                $category->products_count = \App\Models\Product::whereIn('category_id', $allCategoryIds)
                    ->where('is_published', true)
                    ->count();
                return $category;
            });
            
        return Inertia::render('Collections', [
            'categories' => $categories
        ]);
    }
}

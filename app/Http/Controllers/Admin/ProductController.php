<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with('category')->latest();
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        $products = $query->paginate(10)->withQueryString();
        
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_published', true)->count(),
            'draft' => Product::where('is_published', false)->count(),
            'low_stock' => Product::where('stock', '<=', 5)->count()
        ];
        
        return Inertia::render('admin/products/Index', [
            'products' => $products,
            'stats' => $stats,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create()
    {
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return Inertia::render('admin/products/Create', [
            'categories' => $categories
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,draft',
            'images.*' => 'nullable|image|max:2048',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20000',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'nullable|integer',
            'variants.*.weight' => 'nullable|numeric',
            'variants.*.sku' => 'nullable|string',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'is_published' => $validated['status'] === 'active',
            'image' => null, // We'll set this below if an image is uploaded
            'video_path' => null,
        ]);

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('products/videos', 'public');
            $product->update(['video_path' => $videoPath]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);

                // set first image as main thumbnail for convenience
                if ($index === 0) {
                    $product->update(['image' => $path]);
                }
            }
        }

        // Handle variants
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $index => $variant) {
                $product->variants()->create([
                    'name' => $variant['name'],
                    'sku' => $variant['sku'] ?? null,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'] ?? 0,
                    'weight' => $variant['weight'] ?? null,
                    'options' => $variant['options'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return Inertia::render('admin/products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,draft',
            'images.*' => 'nullable|image|max:2048',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20000',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'nullable|integer',
            'variants.*.weight' => 'nullable|numeric',
            'variants.*.sku' => 'nullable|string',
        ]);

        $product->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'is_published' => $validated['status'] === 'active',
        ]);

        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($product->video_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->video_path);
            }
            $videoPath = $request->file('video')->store('products/videos', 'public');
            $product->update(['video_path' => $videoPath]);
        }

        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $existingCount + $index,
                ]);
            }
            
            // update main image if not exists
            if (!$product->image && $product->images()->count() > 0) {
                $product->update(['image' => $product->images()->first()->image_path]);
            }
        }

        // Sync variants
        if ($request->has('variants') && is_array($request->variants)) {
            // Delete old variants
            $product->variants()->delete();
            // Create new ones
            foreach ($request->variants as $index => $variant) {
                $product->variants()->create([
                    'name' => $variant['name'],
                    'sku' => $variant['sku'] ?? null,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'] ?? 0,
                    'weight' => $variant['weight'] ?? null,
                    'options' => $variant['options'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroyImage($id, $imageId)
    {
        $product = Product::findOrFail($id);
        $image = $product->images()->findOrFail($imageId);
        
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $image->delete();

        // if main image deleted, update it
        if ($product->image === $image->image_path) {
            $newMain = $product->images()->first();
            $product->update(['image' => $newMain ? $newMain->image_path : null]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        foreach ($product->images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $query = Category::withCount('products')->with(['parent'])->latest();
        
        if (request('search')) {
            $search = request('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        $categories = $query->paginate(10)->withQueryString();
        
        return Inertia::render('admin/categories/Index', [
            'categories' => $categories,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->get();
            
        return Inertia::render('admin/categories/Create', [
            'parentCategories' => $parentCategories
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,draft',
            'image' => 'nullable|image|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $validated['status'] === 'active';
        unset($validated['status']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->get();
            
        return Inertia::render('admin/categories/Edit', [
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    }

    public function update(\Illuminate\Http\Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,draft',
            'image' => 'nullable|image|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            if ($category->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $validated['status'] === 'active';
        unset($validated['status']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }
}

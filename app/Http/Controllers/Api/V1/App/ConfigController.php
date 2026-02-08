<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use ApiResponser;

    /**
     * Get categories (supports filtering by type).
     * Example: /categories?type=expense
     */
    public function index(Request $request)
    {
        // Mulai query dasar: Default category OR User's category
        $query = Category::where(function ($q) {
            $q->whereNull('user_id')
              ->orWhere('user_id', Auth::id());
        });

        // Filter berdasarkan type jika ada parameter ?type=...
        if ($request->has('type')) {
            $request->validate(['type' => 'in:income,expense']);
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name', 'asc')->get();
            
        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Categories retrieved successfully.', $categories);
    }

    /**
     * Get only public categories (supports filtering by type).
     */
    public function publicIndex(Request $request)
    {
        $query = Category::whereNull('user_id');

        // Filter berdasarkan type jika ada parameter ?type=...
        if ($request->has('type')) {
             $request->validate(['type' => 'in:income,expense']);
             $query->where('type', $request->type);
        }

        $publicCategories = $query->orderBy('name', 'asc')->get();

        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Public categories retrieved successfully.', $publicCategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'type' => 'required|in:income,expense', // <-- Validasi type wajib diisi
        ]);

        $category = Auth::user()->categories()->create($validated);

        return $this->success(201, 'resp_msg_category_created_successfully', 'Category created successfully.', $category);
    }

    public function update(Request $request, Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            return $this->error(403, 'resp_msg_category_update_failed', "You don't have permission to update this category.");
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
            'type' => 'sometimes|required|in:income,expense', // <-- Bisa update type juga
        ]);

        $category->update($validated);
        
        return $this->success(200, 'resp_msg_category_update_successfully', 'Category updated successfully.', $category);
    }

    public function destroy(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            return $this->error(403, 'resp_msg_category_delete_failed', "You don't have permission to delete this category.");
        }

        $category->delete();
        
        return $this->success(200, 'resp_msg_category_delete_successfully', 'Category deleted successfully.');
    }
}
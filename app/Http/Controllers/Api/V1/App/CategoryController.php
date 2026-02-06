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

    public function index()
    {
        // Get default categories and user's custom categories
        $categories = Category::where('user_id', null)
            ->orWhere('user_id', Auth::id())
            ->get();
            
        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Categories retrieved successfully.', $categories);
    }

    /**
     * Get only public (default) categories for guest users.
     */
    public function publicIndex()
    {
        $publicCategories = Category::whereNull('user_id')->get();
        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Public categories retrieved successfully.', $publicCategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $category = Auth::user()->categories()->create($validated);

        return $this->success(201, 'resp_msg_category_created_successfully', 'Category created successfully.', $category);
    }

    public function update(Request $request, Category $category)
    {
        // Pastikan hanya pemilik kategori yang bisa mengedit
        if (Auth::id() !== $category->user_id) {
            return $this->error(403, 'resp_msg_category_update_failed', "You don't have permission to update this category.");
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
        ]);

        $category->update($validated);
        
        return $this->success(200, 'resp_msg_category_update_successfully', 'Category updated successfully.', $category);
    }

    public function destroy(Category $category)
    {
        // Pastikan hanya pemilik kategori yang bisa menghapus
        if (Auth::id() !== $category->user_id) {
            return $this->error(403, 'resp_msg_category_delete_failed', "You don't have permission to delete this category.");
        }

        // Pengecekan transaksi dihapus karena sudah menggunakan soft delete.
        // Data historis akan tetap aman.
        $category->delete();
        
        return $this->success(200, 'resp_msg_category_delete_successfully', 'Category deleted successfully.');
    }
}


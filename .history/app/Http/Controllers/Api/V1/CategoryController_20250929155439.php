<?php

namespace App\Http\Controllers\Api\V1;

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
            
        return $this->success(200, 'Categories retrieved successfully.', $categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7', // Hex color
        ]);

        $category = Auth::user()->categories()->create($validated);

        return $this->success(201, 'Category created successfully.', $category);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
            'color' => 'sometimes|nullable|string|max:7',
        ]);

        $category->update($validated);
        return $this->success(200, 'Category updated successfully.', $category);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Optional: Check if category is used in transactions before deleting
        if ($category->transactions()->exists()) {
            return $this->error(409, 'Cannot delete category because it is associated with existing transactions.');
        }

        $category->delete();
        return $this->success(200, 'Category deleted successfully.');
    }
}


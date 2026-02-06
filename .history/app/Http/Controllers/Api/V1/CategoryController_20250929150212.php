<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil kategori default (user_id is null) DAN kategori milik user
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->get();
            
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string',
            'color_hex' => 'required|string|max:7',
        ]);

        $category = Auth::user()->categories()->create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        // Pastikan user hanya bisa update kategori miliknya
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string',
            'color_hex' => 'sometimes|required|string|max:7',
        ]);

        $category->update($validated);
        
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        // Pastikan user hanya bisa hapus kategori miliknya
        $this->authorize('delete', $category);
        
        $category->delete();
        
        return response()->noContent();
    }
}

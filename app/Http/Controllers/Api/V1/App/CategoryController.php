<?php

namespace App\Http\Controllers\Api\V1\App;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CategoryController extends Controller
{
    use ApiResponser;

    public function index()
    {
        // Ambil kategori utama (parent) milik user atau default sistem
        $categories = Category::whereNull('parent_id')
            ->where(function ($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
            })
            // Eager load subcategories dengan scope yang sama (hindari tarik sub milik user lain)
            ->with(['subcategories' => function ($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
            }])
            ->get();
            
        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Categories retrieved successfully.', $categories);
    }

    /**
     * Endpoint khusus untuk Offline-First Sync
     * Klien mengirim array berisi data yang dibuat/diubah/dihapus secara lokal
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|uuid',
            'categories.*.parent_id' => 'nullable|uuid', // Bisa null untuk parent, berisi UUID untuk child
            'categories.*.name' => 'required|string|max:255',
            'categories.*.icon' => 'required|string|max:255',
            'categories.*.color' => 'required|string|max:20',
            'categories.*.type' => 'required|in:income,expense',
            'categories.*.sort_order' => 'nullable|integer',
            'categories.*.created_at' => 'nullable|date',
            'categories.*.updated_at' => 'nullable|date',
            'categories.*.deleted_at' => 'nullable|date',
        ]);

        $userId = Auth::id();
        $now = now();

        // 1. Format semua data terlebih dahulu menggunakan Carbon
        $formattedCategories = collect($validated['categories'])->map(function ($cat) use ($userId, $now) {
            
            // Pewarisan tipe dari parent (opsional jika dikirim dari klien)
            if (!empty($cat['parent_id'])) {
                // Di sistem batch, mencari data parent langsung ke DB mungkin gagal jika parent-nya 
                // adalah data baru yang ada di batch yang sama. Jadi kita percayakan type & color dari aplikasi klien.
            }

            $createdAt = isset($cat['created_at']) ? Carbon::parse($cat['created_at'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : $now;
            $updatedAt = isset($cat['updated_at']) ? Carbon::parse($cat['updated_at'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : $now;
            $deletedAt = isset($cat['deleted_at']) ? Carbon::parse($cat['deleted_at'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : null;

            return [
                'id'         => $cat['id'],
                'user_id'    => $userId,
                'parent_id'  => $cat['parent_id'] ?? null,
                'name'       => $cat['name'],
                'icon'       => $cat['icon'],
                'color'      => $cat['color'],
                'type'       => $cat['type'],
                'sort_order' => $cat['sort_order'] ?? 0,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => $deletedAt,
            ];
        });

        // 2. PISAHKAN Parent dan Child
        // Ambil data yang parent_id-nya null (Induk Kategori)
        $parents = $formattedCategories->whereNull('parent_id')->values()->toArray();
        
        // Ambil data yang parent_id-nya tidak null (Anak Kategori)
        $children = $formattedCategories->whereNotNull('parent_id')->values()->toArray();

        // 3. UPSERT Parent Dulu
        if (!empty($parents)) {
            Category::upsert(
                $parents,
                ['id'], 
                ['name', 'icon', 'color', 'type', 'sort_order', 'updated_at', 'deleted_at']
            );
        }

        // 4. UPSERT Child Setelah Parent Dipastikan Tersimpan
        if (!empty($children)) {
            Category::upsert(
                $children,
                ['id'], 
                ['parent_id', 'name', 'icon', 'color', 'type', 'sort_order', 'updated_at', 'deleted_at']
            );
        }

        return $this->success(200, 'resp_msg_categories_synced_successfully', 'Categories synced successfully.');
    }


    /**
     * Get only public (default) categories for guest users.
     */
    public function publicIndex()
    {
        $publicCategories = Category::whereNull('parent_id')
            ->whereNull('user_id')
            ->with(['subcategories' => function ($query) {
                $query->whereNull('user_id');
            }])
            ->get();

        return $this->success(200, 'resp_msg_categories_retrieved_successfully', 'Public categories retrieved successfully.', $publicCategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:20', // <-- Validasi warna
            'type' => 'required|in:income,expense',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // Logika Inheritance (Pewarisan)
        if (!empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if ($parent) {
                $validated['type'] = $parent->type;
                $validated['color'] = $parent->color; // <-- Sub-category memaksa mengikuti warna Parent
            }
        }

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
            'icon' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:20', // <-- Validasi warna
            'type' => 'sometimes|required|in:income,expense',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // Jika sub-kategori dipindah ke parent lain, ikuti parent baru
        if (array_key_exists('parent_id', $validated) && !empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if ($parent) {
                $validated['type'] = $parent->type;
                $validated['color'] = $parent->color; // <-- Sub-category memaksa mengikuti warna Parent
            }
        }

        $category->update($validated);

        // EXTRA: Jika yang diupdate ini adalah Parent Category, dan warnanya berubah, 
        // maka update juga warna seluruh sub-kategori di bawahnya.
        if (is_null($category->parent_id) && array_key_exists('color', $validated)) {
            $category->subcategories()->update(['color' => $validated['color']]);
        }
        
        return $this->success(200, 'resp_msg_category_update_successfully', 'Category updated successfully.', $category->load('subcategories'));
    }

    public function destroy(Category $category)
    {
        // Pastikan hanya pemilik kategori yang bisa menghapus
        if (Auth::id() !== $category->user_id) {
            return $this->error(403, 'resp_msg_category_delete_failed', "You don't have permission to delete this category.");
        }

        // Jika parent kategori ini di-delete, pastikan sub-kategori user ini juga ikut di-soft-delete.
        // Anda dapat menambahkan ini agar sub-category tidak yatim (orphan).
        if (is_null($category->parent_id)) {
            $category->subcategories()->where('user_id', Auth::id())->delete();
        }

        $category->delete();
        
        return $this->success(200, 'resp_msg_category_delete_successfully', 'Category deleted successfully.');
    }
}
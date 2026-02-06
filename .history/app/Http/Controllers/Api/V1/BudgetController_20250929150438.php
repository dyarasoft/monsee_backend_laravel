<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    /**
     * Menampilkan semua budget milik pengguna.
     */
    public function index(Request $request)
    {
        $budgets = Auth::user()->budgets()
            ->with('category') // Sertakan info kategori
            ->when($request->has('month'), function ($query) use ($request) {
                return $query->where('month', $request->month);
            })
            ->when($request->has('year'), function ($query) use ($request) {
                return $query->where('year', $request->year);
            })
            ->get();
            
        return response()->json($budgets);
    }

    /**
     * Menyimpan budget baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        // Mencegah duplikasi budget untuk kategori yang sama di bulan & tahun yang sama
        $exists = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Budget untuk kategori ini di periode tersebut sudah ada.'], 422);
        }

        $budget = Auth::user()->budgets()->create($validated);
        
        return response()->json($budget->load('category'), 201);
    }

    /**
     * Menampilkan detail satu budget.
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        return response()->json($budget->load('category'));
    }

    /**
     * Memperbarui budget.
     */
    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $budget->update($validated);

        return response()->json($budget->load('category'));
    }

    /**
     * Menghapus budget.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return response()->noContent();
    }
}


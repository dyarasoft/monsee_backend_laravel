<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    use ApiResponser;

    public function index(Request $request)
    {
        $budgets = Auth::user()->budgets()
            ->with('category')
            ->when($request->has('month'), fn($q) => $q->where('month', $request->month))
            ->when($request->has('year'), fn($q) => $q->where('year', $request->year))
            ->get();
            
        return $this->success($budgets, 'Daftar budget berhasil diambil.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $exists = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return $this->error('Budget untuk kategori ini di periode tersebut sudah ada.', 422);
        }

        $budget = Auth::user()->budgets()->create($validated);
        
        return $this->success($budget->load('category'), 'Budget berhasil disimpan.', 201);
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        return $this->success($budget->load('category'), 'Detail budget berhasil diambil.');
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate(['amount' => 'required|numeric|min:1']);
        $budget->update($validated);

        return $this->success($budget->load('category'), 'Budget berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return $this->success(null, 'Budget berhasil dihapus.');
    }
}


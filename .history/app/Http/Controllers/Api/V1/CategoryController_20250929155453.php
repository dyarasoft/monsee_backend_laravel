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

    public function index()
    {
        $budgets = Auth::user()->budgets()->with('category')->get();
        return $this->success(200, 'Budgets retrieved successfully.', $budgets);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|string|in:monthly,yearly', // Example periods
        ]);

        // Optional: Check if a budget for this category already exists for the user
        $existingBudget = Auth::user()->budgets()
            ->where('category_id', $validated['category_id'])
            ->where('period', $validated['period'])
            ->first();

        if ($existingBudget) {
            return $this->error(409, 'A budget for this category and period already exists.');
        }

        $budget = Auth::user()->budgets()->create($validated);
        return $this->success(201, 'Budget set successfully.', $budget);
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget->update($validated);
        return $this->success(200, 'Budget updated successfully.', $budget);
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return $this->success(200, 'Budget removed successfully.');
    }
}


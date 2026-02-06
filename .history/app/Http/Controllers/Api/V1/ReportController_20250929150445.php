<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generate laporan bulanan untuk dompet tertentu atau semua dompet.
     */
    public function monthlySummary(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'wallet_id' => 'nullable|exists:wallets,id'
        ]);

        $user = Auth::user();
        $query = $user->transactions()
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month);
        
        if ($request->filled('wallet_id')) {
            // Pastikan user hanya bisa mengakses wallet miliknya
            $wallet = $user->wallets()->findOrFail($request->wallet_id);
            $this->authorize('view', $wallet);
            $query->where('wallet_id', $request->wallet_id);
        }

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');
        
        // Mengambil data pengeluaran per kategori untuk pie chart
        $expenseByCategory = (clone $query)->where('type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.color_hex', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'categories.color_hex')
            ->orderBy('total', 'desc')
            ->get();
        
        return response()->json([
            'summary' => [
                'total_income' => (float) $income,
                'total_expense' => (float) $expense,
                'net_profit' => (float) ($income - $expense),
            ],
            'expense_by_category' => $expenseByCategory
        ]);
    }
}


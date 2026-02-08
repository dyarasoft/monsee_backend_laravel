<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ApiResponser;

    /**
     * Menampilkan laporan transaksi berdasarkan rentang tanggal.
     * Support filter: Multiple Wallets.
     */
    public function summary(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'start_date'   => 'required|date_format:Y-m-d',
            'end_date'     => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'wallet_ids'   => 'nullable|array', 
            'wallet_ids.*' => 'integer|exists:wallets,id' 
        ]);

        $user = Auth::user();

        // 2. Base Query
            $query = $user->transactions()
            ->whereDate('transactions.date', '>=', $request->start_date)
            ->whereDate('transactions.date', '<=', $request->end_date);
        
        // 3. Filter Multiple Wallets
        if ($request->filled('wallet_ids')) {
            $validWalletIds = $user->wallets()
                ->whereIn('id', $request->wallet_ids)
                ->pluck('id')
                ->toArray();

            $query->whereIn('transactions.wallet_id', $validWalletIds);
        }

        // 4. Calculation Summary Header (Income, Expense)
        $income  = (clone $query)->where('transactions.type', 'income')->sum('amount');
        $expense = (clone $query)->where('transactions.type', 'expense')->sum('amount');

        // 5. Data for Donut Chart & Top Spending List
        $expenseByCategory = (clone $query)
            ->where('transactions.type', 'expense') 
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.id as category_id',
                'categories.name',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.icon')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->map(function ($item) use ($expense) {
                $item->total_amount = (float) $item->total_amount;
                $item->percentage = $expense > 0 ? round(($item->total_amount / $expense) * 100, 2) : 0;
                return $item;
            });

        // 6. Data for Wallet Trend (Income per Kategori - Optional/Logic check)
        $incomeByCategory = (clone $query)
            ->where('transactions.type', 'income')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.id as category_id',
                'categories.name',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.icon')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->map(function ($item) use ($income) {
                $item->total_amount = (float) $item->total_amount;
                $item->percentage = $income > 0 ? round(($item->total_amount / $income) * 100, 2) : 0;
                return $item;
            });

        // 7. Data for Wallets Breakdown (List Wallet)
        $walletsBreakdown = (clone $query)
            ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
            ->select(
                'wallets.id',
                'wallets.name',
                'wallets.icon',
                DB::raw("SUM(CASE WHEN transactions.type = 'income' THEN transactions.amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN transactions.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense")
            )
            ->groupBy('wallets.id', 'wallets.name', 'wallets.icon')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'icon'          => $item->icon,
                    'total_income'  => (float) $item->total_income,
                    'total_expense' => (float) $item->total_expense,
                ];
            });

        // 8. Response Formatter
        $data = [
            'period' => [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date
            ],
            'summary' => [
                'total_income'  => (float) $income,
                'total_expense' => (float) $expense,
                'net_balance'   => (float) ($income - $expense) 
            ],
            'income_by_category'  => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
            'wallets_breakdown'   => $walletsBreakdown
        ];

        return $this->success(
            200, 
            'resp_msg_report_generated_successfully', 
            'Laporan berhasil digenerate.', 
            $data
        );
    }
}
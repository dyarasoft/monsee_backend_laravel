<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use ApiResponser;

    /**
     * Get transactions with optional filtering by wallet_id and date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate request parameters for filtering
        $validated = $request->validate([
            'wallet_id' => 'required|integer|exists:wallets,id',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        // Find the wallet and authorize the user
        $wallet = Wallet::find($validated['wallet_id']);
        if (Auth::id() !== $wallet->user_id) {
            return $this->error(403, "You don't have permission to access this wallet.");
        }

        // Build the base query for transactions
        $transactionsQuery = $wallet->transactions()->with('category');

        // Apply date filters if they exist
        if (isset($validated['start_date'])) {
            $transactionsQuery->where('date', '>=', $validated['start_date']);
        }
        if (isset($validated['end_date'])) {
            $transactionsQuery->where('date', '<=', $validated['end_date']);
        }

        // Get the filtered list of transactions, ordered by the latest
        $transactions = $transactionsQuery->latest('date')->get();

        // --- Calculate total spending per category for the same filtered range ---
        $categorySpendingQuery = $wallet->transactions()
                                    ->where('type', 'expense')
                                    ->join('categories', 'transactions.category_id', '=', 'categories.id')
                                    ->select('categories.name as category_name', 'categories.icon', DB::raw('SUM(transactions.amount) as total_amount'))
                                    ->groupBy('categories.name', 'categories.icon');

        // Apply the same date filters to the spending query
        if (isset($validated['start_date'])) {
            $categorySpendingQuery->where('date', '>=', $validated['start_date']);
        }
        if (isset($validated['end_date'])) {
            $categorySpendingQuery->where('date', '<=', $validated['end_date']);
        }

        $categorySpending = $categorySpendingQuery->get();

        // Combine results into a single data object
        $data = [
            'transactions' => $transactions,
            'category_summary' => $categorySpending,
        ];

        return $this->success(200, 'Transactions retrieved successfully.', $data);
    }


    /**
     * Store a newly created transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Ensure the user can only create a transaction in their own wallet
        $wallet = Auth::user()->wallets()->find($validated['wallet_id']);
        if (!$wallet) {
            return $this->error(403, "You don't have permission to access this wallet.");
        }

        $validated['user_id'] = Auth::id();

        $transaction = Transaction::create($validated);

        return $this->success(201, 'Transaction recorded successfully.', $transaction);
    }
}


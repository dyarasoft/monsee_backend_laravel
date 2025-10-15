<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Ubah validasi agar tanggal menjadi opsional
        $request->validate([
            'wallet_id' => 'required|integer|exists:wallets,id',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $wallet = auth()->user()->wallets()->find($request->wallet_id);
        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }

        // 2. Buat query dasar untuk transaksi
        $transactionsQuery = Transaction::where('user_id', auth()->id())
            ->where('wallet_id', $request->wallet_id)
            ->with(['category', 'wallet']);

        // 3. Buat query dasar untuk ringkasan kategori
        $categorySummaryQuery = Transaction::where('transactions.user_id', auth()->id())
            ->where('transactions.wallet_id', $request->wallet_id)
            ->where('transactions.type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id');

        // 4. Terapkan filter tanggal secara kondisional jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $transactionsQuery->whereBetween('date', [$request->start_date, $request->end_date]);
            $categorySummaryQuery->whereBetween('transactions.date', [$request->start_date, $request->end_date]);
        }

        // 5. Eksekusi query
        $transactions = $transactionsQuery->latest()->get();
        $categorySummary = $categorySummaryQuery
            ->select('categories.name as category_name', 'categories.icon', DB::raw('SUM(transactions.amount) as total_amount'))
            ->groupBy('transactions.category_id', 'categories.name', 'categories.icon')
            ->get();

        return response()->json([
            'status_code' => 200,
            'message_code' => 'resp_msg_transactions_retrieved_successfully',
            'message' => 'Transactions retrieved successfully.',
            'data' => [
                'transactions' => $transactions,
                'category_summary' => $categorySummary,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|integer|exists:wallets,id',
            'category_id' => 'required|integer|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date_format:Y-m-d',
        ]);

        // Create a new transaction associated with the authenticated user
        $transaction = auth()->user()->transactions()->create($request->all());

        return response()->json([
            'status_code' => 201,
            'message_code' => 'resp_msg_transaction_created_successfully',
            'message' => 'Transaction created successfully.',
            'data' => $transaction
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        // Ensure the user can only view their own transactions
        if ($transaction->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Transaction retrieved successfully.',
            'data' => $transaction->load(['category', 'wallet'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Ensure the user can only update their own transactions
        if ($transaction->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'sometimes|required|date_format:Y-m-d',
        ]);

        $transaction->update($request->all());

        return response()->json([
            'status_code' => 200,
            'message' => 'Transaction updated successfully.',
            'data' => $transaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Ensure the user can only delete their own transactions
        if ($transaction->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Transaction deleted successfully.',
        ]);
    }
}
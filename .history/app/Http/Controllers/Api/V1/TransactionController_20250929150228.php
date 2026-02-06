<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Auth::user()->transactions()
            ->with(['category', 'wallet']) // Eager load relasi
            ->when($request->has('wallet_id'), function ($query) use ($request) {
                return $query->where('wallet_id', $request->wallet_id);
            })
            ->latest('date') // Urutkan berdasarkan tanggal terbaru
            ->paginate(20);

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Authorization check: pastikan wallet_id milik user yang login
        $wallet = Auth::user()->wallets()->findOrFail($validated['wallet_id']);

        $transaction = $wallet->transactions()->create(array_merge(
            $validated,
            ['user_id' => Auth::id()]
        ));

        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return response()->json($transaction->load(['category', 'wallet']));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'wallet_id' => 'sometimes|required|exists:wallets,id',
            'category_id' => 'sometimes|required|exists:categories,id',
            'type' => 'sometimes|required|in:income,expense',
            'amount' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
            'date' => 'sometimes|required|date',
        ]);
        
        $transaction->update($validated);
        
        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();
        return response()->noContent();
    }
}

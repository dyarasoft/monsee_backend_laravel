<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date', // Menambahkan validasi untuk tanggal
        ]);

        // Memastikan user hanya bisa membuat transaksi di dompet miliknya
        $wallet = Auth::user()->wallets()->find($validated['wallet_id']);
        if (!$wallet) {
            return $this->error(403, "You don't have permission to access this wallet.");
        }
        
        // Menambahkan user_id ke data yang divalidasi
        $validated['user_id'] = Auth::id();

        $transaction = Transaction::create($validated);

        return $this->success(201, 'Transaction recorded successfully.', $transaction);
    }
}


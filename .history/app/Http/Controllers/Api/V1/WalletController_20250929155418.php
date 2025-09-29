<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $wallets = Auth::user()->wallets;
        return $this->success(200, 'Wallets retrieved successfully.', $wallets);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $currentWalletCount = $user->wallets()->count();

        if ($currentWalletCount >= $user->wallet_limit) {
            return $this->error(403, 'You have reached your wallet limit. Watch an ad to increase it.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:255',
        ]);

        $wallet = $user->wallets()->create($validated);

        return $this->success(201, 'Wallet created successfully.', $wallet);
    }

    public function show(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        return $this->success(200, 'Wallet retrieved successfully.', $wallet->load('transactions'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        $this->authorize('update', $wallet);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
        ]);

        $wallet->update($validated);

        return $this->success(200, 'Wallet updated successfully.', $wallet);
    }

    public function destroy(Wallet $wallet)
    {
        $this->authorize('delete', $wallet);
        $wallet->delete();
        return $this->success(200, 'Wallet deleted successfully.');
    }
}


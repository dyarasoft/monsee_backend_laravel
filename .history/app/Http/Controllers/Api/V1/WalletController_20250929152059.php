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
        $wallets = Auth::user()->wallets()->get();
        return $this->success($wallets, 'Daftar dompet berhasil diambil.');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $walletCount = $user->wallets()->count();

        if ($walletCount >= $user->wallet_limit) {
            return $this->error('Anda telah mencapai batas maksimal dompet.', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:255',
        ]);

        $wallet = $user->wallets()->create($validated);

        return $this->success($wallet, 'Dompet berhasil dibuat.', 201);
    }

    public function show(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        return $this->success($wallet, 'Detail dompet berhasil diambil.');
    }

    public function update(Request $request, Wallet $wallet)
    {
        $this->authorize('update', $wallet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $wallet->update($validated);
        return $this->success($wallet, 'Dompet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet)
    {
        $this->authorize('delete', $wallet);
        $wallet->delete();
        return $this->success(null, 'Dompet berhasil dihapus.');
    }
}


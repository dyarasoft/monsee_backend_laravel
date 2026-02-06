<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wallets = $request->user()->wallets()->get();
        return WalletResource::collection($wallets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // LOGIKA PENGECEKAN BATAS DOMPET
        if ($user->wallets()->count() >= $user->wallet_limit) {
            return response()->json([
                'message' => 'Anda telah mencapai batas maksimal dompet. Tonton iklan untuk menambah slot.'
            ], 403); // 403 Forbidden
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'initial_balance' => 'required|numeric|min:0',
        ]);
        
        $wallet = $user->wallets()->create($validated);

        return new WalletResource($wallet);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        return new WalletResource($wallet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        $this->authorize('update', $wallet);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:50',
        ]);

        $wallet->update($validated);
        
        return new WalletResource($wallet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        $this->authorize('delete', $wallet);
        $wallet->delete();
        return response()->noContent();
    }
}


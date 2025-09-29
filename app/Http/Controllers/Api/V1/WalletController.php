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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $wallets = Auth::user()->wallets;
        return $this->success(200, 'Wallets retrieved successfully.', $wallets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
        ]);

        $wallet = Auth::user()->wallets()->create($validated);

        return $this->success(201, 'Wallet created successfully.', $wallet);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $wallet = Auth::user()->wallets()->find($id);

        if (!$wallet) {
            return $this->error(404, 'Wallet not found.');
        }

        return $this->success(200, 'Wallet details retrieved successfully.', $wallet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $wallet = Auth::user()->wallets()->find($id);

        if (!$wallet) {
            return $this->error(404, 'Wallet not found.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:255',
        ]);

        $wallet->update($validated);

        return $this->success(200, 'Wallet updated successfully.', $wallet);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $wallet = Auth::user()->wallets()->find($id);

        if (!$wallet) {
            return $this->error(404, 'Wallet not found.');
        }

        $wallet->delete();

        return $this->success(200, 'Wallet deleted successfully.');
    }
}


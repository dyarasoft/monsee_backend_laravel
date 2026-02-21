<?php

namespace App\Http\Controllers\Api\V1\App;

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
        return response()->json([
            'status_code' => 200,
            'message_code' => 'resp_msg_wallets_retrieved_successfully',
            'message' => 'Wallets retrieved successfully.',
            'data' => $wallets
        ]);
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
            'icon' => 'required|string|max:255'
        ]);

        $wallet = Auth::user()->wallets()->create($validated);
        return response()->json([
            'status_code' => 201,
            'message_code' => 'resp_msg_wallet_created_successfully',
            'message' => 'Wallet created successfully.',
            'data' => $wallet
        ], 201);

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
            return response()->json([
                'status_code' => 404,
                'message_code' => 'resp_msg_wallet_not_found',
                'message' => 'Wallet not found.'
            ]);
        }

        return response()->json([
            'status_code' => 200,
            'message_code' => 'resp_msg_wallet_details_retrieved_successfully',
            'message' => 'Wallet details retrieved successfully.',
            'data' => $wallet
        ]);

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
            return response()->json([
                'status_code' => 404,
                'message_code' => 'resp_msg_wallet_not_found',
                'message' => 'Wallet not found.'
            ]);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:255'
        ]);

        $wallet->update($validated);

         return response()->json([
            'status_code' => 200,
            'message_code' => 'resp_msg_wallet_updated_successfully',
            'message' => 'Wallet updated successfully.',
            'data' => $wallet
        ]);
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
           return response()->json([
                'status_code' => 404,
                'message_code' => 'resp_msg_wallet_not_found',
                'message' => 'Wallet not found.'
            ]);
        }

        $wallet->delete();

        return response()->json([
            'status_code' => 200,
            'message_code' => 'resp_msg_wallet_deleted_successfully',
            'message' => 'Wallet deleted successfully.'
        ]);

        return $this->success(200, 'Wallet deleted successfully.');
    }
}


<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tambah batas dompet pengguna setelah menonton rewarded ad.
     */
    public function increaseWalletLimit(Request $request)
    {
        $user = $request->user();
        $user->wallet_limit += 1;
        $user->save();

        return response()->json([
            'message' => 'Batas dompet berhasil ditambah.',
            'new_limit' => $user->wallet_limit,
        ]);
    }

    // Tambahkan fungsi untuk unlock fitur lain di sini
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponser;

    /**
     * Increase the user's wallet limit after a successful rewarded ad.
     */
    public function increaseWalletLimit(Request $request)
    {
        $user = Auth::user();
        $user->wallet_limit += 1;
        $user->save();

        return $this->success(200, 'Wallet limit increased successfully.', [
            'new_limit' => $user->wallet_limit
        ]);
    }
}


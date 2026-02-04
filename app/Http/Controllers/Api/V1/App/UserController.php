<?php

namespace App\Http\Controllers\Api\V1\App;

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

    public function deactivateAccount(Request $request)
{
    $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    $user = Auth::user();

    
    $user->update([
        'deleted_by'     => $user->id,        // ID user itu sendiri
        'deleted_reason' => $request->reason, // Alasan dari input
    ]);


    $user->tokens()->delete();

    $user->delete(); 
   

    return response()->json([
        'status_code' => 200,
        'message'     => 'Account deactivated successfully.',
    ]);
}
}


<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email', // Aturan 'unique' dihapus untuk custom handling
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Cek manual apakah email sudah ada
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // Jika user ada & punya google_id (tapi tidak punya password), berarti mendaftar via Google
            if ($existingUser->google_id && is_null($existingUser->password)) {
                return $this->error(409, 'This email is registered with a Google account. Please log in using Google.');
            }
            // Jika user ada & punya password, berarti pendaftaran duplikat biasa
            return $this->error(409, 'An account with this email already exists. Please log in.');
        }

        // Jika tidak ada user, lanjutkan proses registrasi
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(201, 'Registration successful. A verification link has been sent to your email.', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return $this->error(404, 'Account not found. Please check your email.');
        }
        
        // Cek jika akun hanya punya google_id (tidak punya password)
        if (is_null($user->password) && $user->google_id) {
            return $this->error(401, 'This account was created using Google. Please log in with Google.');
        }

        // Check password
        if (!Hash::check($validated['password'], $user->password)) {
            return $this->error(401, 'Wrong password. Please try again.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(200, 'Login successful.', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(200, 'Logout successful.');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return $this->success(200, 'User data retrieved successfully.', Auth::user());
    }

    /**
     * Handle login from Google Sign-In on mobile apps.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithGoogle(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $googleUser = Socialite::driver('google')->userFromToken($validated['token']);
            $userEmail = $googleUser->getEmail();
            $googleId = $googleUser->getId();

            // Cari user berdasarkan email terlebih dahulu
            $user = User::where('email', $userEmail)->first();

            if ($user) {
                // Jika user ada, tautkan google_id nya jika belum ada
                if (is_null($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->email_verified_at = now(); // Anggap terverifikasi karena dari Google
                    $user->save();
                }
            } else {
                // Jika user tidak ada, buat user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $userEmail,
                    'google_id' => $googleId,
                    'email_verified_at' => now(),
                    'password' => null, // Tidak perlu password
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success(200, 'Google login successful.', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return $this->error(401, 'Invalid Google token or login failed.');
        }
    }


    // These methods below are for web-based flow, not used by mobile app.
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->id],
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => null, // No password
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // This part would need to be adapted to return the token to a web frontend.
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return $this->error(500, 'Something went wrong with Google authentication.');
        }
    }
}


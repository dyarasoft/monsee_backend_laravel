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

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validation is handled globally by Handler.php on failure
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];

        return $this->success(201, 'User registered successfully.', $data);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email first
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            return $this->error(404, 'The account with this email was not found.');
        }

        // Then, attempt to authenticate. If it fails, the password is wrong.
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error(401, 'The password you entered is incorrect.');
        }
        
        // Retrieve the authenticated user instance
        $authenticatedUser = Auth::user();
        $token = $authenticatedUser->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $authenticatedUser,
        ];

        return $this->success(200, 'Login successful.', $data);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(200, 'You have been successfully logged out.');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        // Returns a URL for the frontend to open
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return $this->success(200, 'Redirect URL generated.', ['redirect_url' => $redirectUrl]);
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->id],
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => null, // No password needed for social logins
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ];

            return $this->success(200, 'Google login successful.', $data);
        } catch (\Exception $e) {
            return $this->error(500, 'An error occurred during Google authentication.');
        }
    }
}


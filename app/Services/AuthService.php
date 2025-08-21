<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Service;
use Exception;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService extends Service
{
    /**
     * Register a new user.
     *
     * @param array $data ['email', 'password']
     * @return array Response with message, status, and data
     */
    public function register($data)
    {
        try {
            // Create new user
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Generate access token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('User created successfully.', 200, [
                'token' => $token,
            ]);
        } catch (Exception $e) {
            Log::error('Error while creating user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while creating the user. Please try again.', 500);
        }
    }

    /**
     * Login a user with email and password.
     *
     * @param array $data ['email', 'password']
     * @return array Response with message, user data, and access token
     */
    public function login($data)
    {
        try {
            $user = User::where('email', $data['email'])->first();

            // Check if user exists and password matches
            if (!$user || !Hash::check($data['password'], $user->password)) {
                return $this->errorResponse('Invalid credentials.', 422);
            }

            // Generate a new access token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('Login successful.', 200, [
                'token' => $token,
                'user' => new UserResource($user),
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (Exception $e) {
            Log::error('Error while logging in user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during login. Please try again.', 500);
        }
    }

    /**
     * Logout the current user and delete their access token.
     *
     * @return array Response with success or error message
     */
    public function logout()
    {
        try {
           $user= Auth::guard('sanctum')->user(); // Get current authenticated user
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete(); // Delete current token
            }

            return $this->successResponse('Logout successful.', 200);
        } catch (Exception $e) {
            Log::error('Error while logging out user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during logout. Please try again.', 500);
        }
    }

    /**
     * Login using Google OAuth token.
     *
     * @param string $googleToken Google ID token
     * @return array Response with user data and access token
     */
    public function loginWithGoogle(string $googleToken)
    {
        try {
            // Initialize Google Client to verify token
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($googleToken);

            if (!$payload) {
                return $this->errorResponse('Invalid Google token.', 401);
            }

            $email = $payload['email']; // Get email from Google payload

            // Find user in database
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create new user if not found
                $user = User::create([
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)), // Random password
                ]);
            }

            // Login user and create access token
            Auth::login($user);
            $token = $user->createToken('authToken')->plainTextToken;

            return $this->successResponse('Login successful.', 200, [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            Log::error('Error while logging in with Google: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during Google login. Please try again.', 500);
        }
    }
}

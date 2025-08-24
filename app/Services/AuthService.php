<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;
use Google_Client;
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

            return $this->successResponse('تم إنشاء المستخدم بنجاح.', 200, [
                'token' => $token,
            ]);
        } catch (Exception $e) {
            Log::error('Error while creating user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء المستخدم. يرجى المحاولة مرة أخرى.', 500);
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
            // Find user by email
            $user = User::where('email', $data['email'])->first();

            // Validate password
            if (!$user || !Hash::check($data['password'], $user->password)) {
                return $this->errorResponse('بيانات تسجيل الدخول غير صحيحة.', 422);
            }

            // Generate a new access token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('تم تسجيل الدخول بنجاح.', 200, [
                'token' => $token,
                'user' => new UserResource($user),
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (Exception $e) {
            Log::error('Error while logging in user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.', 500);
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
            // Get current authenticated user
            $user = Auth::guard('sanctum')->user();
            if ($user && $user->currentAccessToken()) {
                // Delete current token
                $user->currentAccessToken()->delete();
            }

            return $this->successResponse('تم تسجيل الخروج بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('Error while logging out user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الخروج. يرجى المحاولة مرة أخرى.', 500);
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
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $googleToken,
        ]);

        if ($response->failed()) {
            return $this->errorResponse('رمز Google غير صالح.', 401);
        }

        $payload = $response->json();


        $email = $payload['email'];


        $user = User::firstOrCreate(
            ['email' => $email],
            ['password' => bcrypt(Str::random(16))]
        );

        Auth::login($user);
        $token = $user->createToken('authToken')->plainTextToken;

        return $this->successResponse('تم تسجيل الدخول باستخدام Google بنجاح.', 200, [
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    } catch (\Exception $e) {
        Log::error('Error while logging in with Google: ' . $e->getMessage());
        return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول باستخدام Google.', 500);
    }
}
}

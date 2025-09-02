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
use Exception;

class AuthService extends Service
{
    /**
     * Register a new user.
     */
    public function register($data)
    {
        try {
            $activationCode = Str::lower(Str::random(6));

            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'activation_code' => $activationCode,
            ]);

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
     * Prevent multiple logins for the same user.
     */
    public function login($data)
{
    try {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('بيانات تسجيل الدخول غير صحيحة.', 422);
        }
if (
    $user->tokens()->count() > 0   // عنده تسجيل دخول مسبق
    && $user->is_admin == 0        // مو أدمن
    && $user->is_active == 1       // مفعل
    && (
        $user->name !== null ||               // مكمل بياناته (واحد على الأقل من الحقول مو فاضي)
        $user->average !== null ||
        $user->gender !== null ||
        $user->branch_id !== null
    )
) {
    return $this->errorResponse('أنت مسجل دخول بالفعل من جهاز آخر.', 403);
}





        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('تم تسجيل الدخول بنجاح.', 200, [
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    } catch (Exception $e) {
        Log::error('Error while logging in user: ' . $e->getMessage());
        return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.', 500);
    }
}

    /**
     * Logout the current authenticated user (current token only).
     */
    public function logout()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                Log::info('User tokens count before logout: ' . $user->tokens()->count());

                // حذف التوكن الحالي فقط
                auth()->user()->tokens()->delete();
            }

            return $this->successResponse('تم تسجيل الخروج بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('Error while logging out user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الخروج. يرجى المحاولة مرة أخرى.', 500);
        }
    }


    /**
     * Login using Google OAuth token.
     * Prevent multiple logins for the same user.
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
            $activationCode = Str::lower(Str::random(6));

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'password' => bcrypt(Str::random(16)),
                    'activation_code' => $activationCode,
                ]
            );


if (
    $user->tokens()->count() > 0   // عنده تسجيل دخول مسبق
    && $user->is_admin == 0        // مو أدمن
    && $user->is_active == 1       // مفعل
    && (
        $user->name !== null ||               // مكمل بياناته (واحد على الأقل من الحقول مو فاضي)
        $user->average !== null ||
        $user->gender !== null ||
        $user->branch_id !== null
    )
) {
    return $this->errorResponse('أنت مسجل دخول بالفعل من جهاز آخر.', 403);
}

            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('تم تسجيل الدخول باستخدام Google بنجاح.', 200, [
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (Exception $e) {
            Log::error('Error while logging in with Google: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول باستخدام Google.', 500);
        }
    }
}

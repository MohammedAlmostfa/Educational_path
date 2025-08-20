<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;
use Google_Client;

class AuthService extends Service
{
    public function register($data)
    {
        try {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('تم إنشاء المستخدم بنجاح.', 200, [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            Log::error('Error while creating user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء المستخدم، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function login($data)
    {
        try {
            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['بيانات الدخول غير صحيحة.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('تم تسجيل الدخول بنجاح.', 200, [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 500);
        } catch (Exception $e) {
            Log::error('Error while login user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function logout($user)
    {
        try {
            $user->currentAccessToken()->delete();
            return $this->successResponse('تم تسجيل الخروج بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('Error while logout user: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الخروج، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function loginWithGoogle(string $googleToken)
    {
        try {
            // تهيئة Google Client
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($googleToken);

            if (!$payload) {
                return $this->errorResponse('توكن Google غير صالح.', 401);
            }

            // جلب بيانات المستخدم من Google
            $email = $payload['email'];

            // البحث عن المستخدم
            $user = User::where('email', $email)->first();

            if (!$user) {
                // إنشاء مستخدم جديد
                $user = User::create([
                    'email' => $email,
                    'password' => bcrypt(Str::random(16))
                ]);
            }

            // تسجيل الدخول وإصدار توكن
            Auth::login($user);
            $token = $user->createToken('authToken')->plainTextToken;

            return $this->successResponse('تم تسجيل الدخول بنجاح.', 200, [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (Exception $e) {
            Log::error('Error while Google login: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تسجيل الدخول باستخدام Google، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

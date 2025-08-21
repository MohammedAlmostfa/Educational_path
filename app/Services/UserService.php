<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService extends Service
{
    public function getAllUser()
    {
        try {
            $users = User::paginate(10);
            return $this->successResponse('تم جلب المستخدمين بنجاح', $users, 200);
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء جلب المستخدمين: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة المستخدمين، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function GeneratCode($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->activation_code == null) {
                $randomNumber = rand(1000, 9999);
                $user->activation_code = $randomNumber;
                $user->save();

                return $this->successResponse('تم توليد الكود بنجاح', $randomNumber, 200);
            } else {
                return $this->errorResponse('المستخدم له كود سابق', 400);
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء توليد الكود: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء توليد الكود، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function checkCode($data)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->errorResponse('المستخدم غير موجود', 404);
            }

            if ($user->activation_code == $data['activation_code']) {
                return $this->successResponse('تم التحقق من الكود', 200);
            } else {
                return $this->errorResponse('الكود المستخدم خاطئ', 400);
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء التحقق من الكود: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء التحقق من الكود، يرجى المحاولة مرة أخرى.', 500);
        }
    }
public function AddUserData($data)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('المستخدم غير موجود', 404);
        }

        $user->update([
            "average" => $data['average'],
            "gender" => $data['gender'],
            "branch" => $data['branch'],
        ]);

        return $this->successResponse('تم تحديث بيانات المستخدم بنجاح', $user, 200);

    } catch (Exception $e) {
        Log::error('حدث خطأ أثناء تحديث بيانات المستخدم: ' . $e->getMessage());
        return $this->errorResponse('حدث خطأ أثناء تحديث بيانات المستخدم، يرجى المحاولة مرة أخرى.', 500);
    }
}

}

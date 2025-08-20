<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DeviceTokenService extends Service
{
    public function createOrUpdate(array $data)
    {
        try {
            // جلب الجهاز إذا كان موجودًا
            $device = DeviceToken::where('device_id', $data['device_id'])->first();

            // الحصول على المستخدم حالياً إذا كان مسجل دخول
            $user = Auth::user(); // null إذا كان المستخدم غير مسجل

            if ($device) {
                // تحديث التوكن إذا تغير
                if ($device->fcm_token !== $data['fcm_token']) {
                    $device->fcm_token = $data['fcm_token'];
                }

                // تحديث الـ user_id فقط إذا كان المستخدم مسجل دخول و user_id مختلف
                if ($user && $device->user_id !== $user->id) {
                    $device->user_id = $user->id;
                }

                $device->save();
            } else {
                // إنشاء سجل جديد
                DeviceToken::create([
                    'device_id' => $data['device_id'],
                    'fcm_token' => $data['fcm_token'],
                    'user_id' => $user->id ?? null, // null إذا كان المستخدم غير مسجل
                ]);
            }

            return $this->successResponse('تم التحقق من FCM بنجاح.', 200);

        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء معالجة FCM: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة FCM، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

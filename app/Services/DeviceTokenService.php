<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for handling device token operations.
 *
 * This service handles creating or updating device tokens for push notifications.
 */
class DeviceTokenService extends Service
{
    /**
     * Create or update a device token.
     *
     * @param array $data An array containing 'device_id' and 'fcm_token'.
     * @return array Standardized response with status and message.
     */
    public function createOrUpdate(array $data)
    {
        try {
            // Retrieve the device if it already exists
            $device = DeviceToken::where('device_id', $data['device_id'])->first();

            // Get the currently authenticated user using Sanctum (null if not logged in)
            $user = Auth::guard('sanctum')->user();

            if ($device) {
                // Update the FCM token if it has changed
                if ($device->fcm_token !== $data['fcm_token']) {
                    $device->fcm_token = $data['fcm_token'];
                }

                // Update the user_id only if a user is logged in and it's different
                if ($user && $device->user_id !== $user->id) {
                    $device->user_id = $user->id;
                }

                // Save changes to the device
                $device->save();
            } else {
                // Create a new device token record
                DeviceToken::create([
                    'device_id' => $data['device_id'],
                    'fcm_token' => $data['fcm_token'],
                    'user_id' => $user->id ?? null, // null if user is not logged in
                ]);
            }

            return $this->successResponse('تم التحقق من رمز FCM بنجاح.', 200);

        } catch (Exception $e) {
            // Log any exception for debugging
            Log::error('Error while processing FCM token: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة رمز FCM. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

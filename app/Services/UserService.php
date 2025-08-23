<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for handling user-related operations.
 *
 * This service manages user accounts, activation codes, and profile data.
 */
class UserService extends Service
{
    /**
     * Get all users that don't have an activation code.
     *
     * @return array Response with paginated users
     */
    public function getAllUser($filteringData)
    {
        try {

            $users = User::where('activation_code', null)
                ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                ->orderByDesc('created_at')
                ->paginate(10);
            return $this->successResponse('تم جلب المستخدمين بنجاح', 200, $users);
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء جلب المستخدمين: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة المستخدمين، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Generate an activation code for a user if not already exists.
     *
     * @param int $id User ID
     * @return array Response with activation code
     */
    public function Activaccount($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->activation_code == null) {
                $randomNumber = rand(1000, 9999);
                $user->activation_code = $randomNumber;
                $user->save();

                return $this->successResponse('تم توليد الكود بنجاح', 200, ["activation_code" => $randomNumber]);
            } else {
                return $this->errorResponse('المستخدم له كود سابق', 400);
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء توليد الكود: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء توليد الكود، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Check if the activation code matches the current logged-in user.
     *
     * @param array $data Activation code
     * @return array Response with verification result
     */
    public function checkActivationCode($data)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير موجود', 404);
            }

            if ($user->activation_code == $data['activation_code']) {
                $user->is_active = '1';
                $user->save();
                return $this->successResponse('تم التحقق من الكود', 200);
            } else {
                return $this->errorResponse('الكود المستخدم خاطئ', 400);
            }
        } catch (\Exception $e) {
            Log::error('حدث خطأ أثناء التحقق من الكود: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء التحقق من الكود، يرجى المحاولة مرة أخرى.', 500);
        }
    }


    /**
     * Add or update extra user profile data (average, gender, branch).
     *
     * @param array $data User data
     * @return array Response with updated user
     */
    public function AddUserData($data)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return $this->errorResponse('المستخدم غير موجود', 404);
            }

            $user->update([
                "average" => $data['average'],
                "gender" => $data['gender'],
                "branch_id" => $data['branch_id'],
            ]);

            return $this->successResponse('تم تحديث بيانات المستخدم بنجاح', 200, $user);
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء تحديث بيانات المستخدم: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث بيانات المستخدم، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

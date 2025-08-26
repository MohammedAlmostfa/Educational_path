<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService
 *
 * Service responsible for user operations:
 * - Retrieve users
 * - Manage activation codes
 * - Add or update user profile data
 */
class UserService extends Service
{
    /**
     * Get all users without an activation code, with optional filtering.
     *
     * @param array $filteringData Filtering parameters
     * @return array Standardized response with users
     */
    public function getAllUser($filteringData)
    {
        try {
            $users = User::where('is_active', 0)
                ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                ->orderByDesc('created_at')
                ->paginate(10);

            return $this->successResponse('تم جلب المستخدمين بنجاح', 200, $users);

        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة المستخدمين، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    // /**
    //  * Generate an activation code for a user if not exists.
    //  *
    //  * @param int $id User ID
    //  * @return array Standardized response with the code
    //  */
    // public function Activaccount($id)
    // {
    //     try {
    //         $user = User::findOrFail($id);

    //         if ($user->activation_code == null) {
    //             $randomNumber = rand(1000, 9999);
    //             $user->activation_code = $randomNumber;
    //             $user->save();

    //             return $this->successResponse('تم توليد الكود بنجاح', 200, ["activation_code" => $randomNumber]);
    //         } else {
    //             return $this->errorResponse('المستخدم له كود سابق', 400);
    //         }

    //     } catch (Exception $e) {
    //         Log::error('Error generating activation code: ' . $e->getMessage());
    //         return $this->errorResponse('حدث خطأ أثناء توليد الكود، يرجى المحاولة مرة أخرى.', 500);
    //     }
    // }

    /**
     * Verify the activation code for the currently authenticated user.
     *
     * @param array $data ['activation_code']
     * @return array Response indicating success or failure
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

        } catch (Exception $e) {
            Log::error('Error verifying activation code: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء التحقق من الكود، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Add new user profile data (average, gender, branch) if not set previously.
     *
     * @param array $data User profile data
     * @return array Response with updated user data
     */
    public function addUserData(array $data)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير موجود', 404);
            }

            if ($user->average !== null || $user->gender !== null || $user->branch_id !== null) {
                return $this->errorResponse('تم تعيين البيانات سابقًا', 400);
            }

            $user->update([
                "average"   => $data['average'] ?? null,
                "gender"    => $data['gender'] ?? null,
                "branch_id" => $data['branch_id'] ?? null,
            ]);

            return $this->successResponse('تم اضافة بيانات المستخدم بنجاح', 200, $user);

        } catch (Exception $e) {
            Log::error('Error adding user data: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء اضافة بيانات المستخدم، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Update existing user profile data.
     *
     * @param array $data User profile data
     * @return array Response with updated user data
     */
    public function updateUserData(array $data)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير موجود', 404);
            }

            $user->update([
                "average"   => $data['average'] ?? $user->average,
                "gender"    => $data['gender'] ?? $user->gender,
                "branch_id" => $data['branch_id'] ?? $user->branch_id,
            ]);

            return $this->successResponse('تم تحديث بيانات المستخدم بنجاح', 200, $user);

        } catch (Exception $e) {
            Log::error('Error updating user data: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث بيانات المستخدم، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Get the currently authenticated user's data.
     *
     * @return array Response with user data
     */
    public function getUserData()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                return $this->successResponse('تم التحقق من المستخدم', 200, $user);
            } else {
                return $this->errorResponse('المستخدم غير مصرح به أو غير موجود', 401);
            }
        } catch (Exception $e) {
            Log::error('Error fetching user data: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء التحقق من المستخدم، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

<?php

namespace App\Services;

use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for handling user's saved colleges.
 * 
 * This service allows adding, removing, and listing saved colleges for a user.
 */
class SavedCollegeService extends Service
{
    /**
     * Add a college to the authenticated user's saved list.
     *
     * @param int $collegeId
     * @return array Response with status and message
     */
    public function addSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            $college = College::findOrFail($collegeId);

            // Attach the college if not already saved
            if (!$user->savedColleges()->where('college_id', $collegeId)->exists()) {
                $user->savedColleges()->attach($collegeId);
                return $this->successResponse('تم حفظ الكلية بنجاح', 200);
            } else {
                return $this->errorResponse('الكلية محفوظة مسبقاً', 400);
            }

        } catch (Exception $e) {
            Log::error('Error while saving college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حفظ الكلية', 500);
        }
    }

    /**
     * Remove a college from the authenticated user's saved list.
     *
     * @param int $collegeId
     * @return array Response with status and message
     */
    public function removeSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            // Detach the college if it exists in saved list
            if ($user->savedColleges()->where('college_id', $collegeId)->exists()) {
                $user->savedColleges()->detach($collegeId);
                return $this->successResponse('تمت إزالة الكلية من المحفوظات', 200);
            } else {
                return $this->errorResponse('الكلية غير موجودة في المحفوظات', 400);
            }

        } catch (Exception $e) {
            Log::error('Error while removing saved college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إزالة الكلية من المحفوظات', 500);
        }
    }

    /**
     * Retrieve all saved colleges for the authenticated user.
     *
     * @return array Response with status, message, and data
     */
    public function getSaved()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            $saved = $user->savedColleges()->with(['university', 'departments'])->get();

            return $this->successResponse('تم جلب الكليات المحفوظة بنجاح', 200, $saved);

        } catch (Exception $e) {
            Log::error('Error while fetching saved colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات المحفوظة', 500);
        }
    }
}

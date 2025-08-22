<?php

namespace App\Services;

use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for handling user's favorite colleges.
 * 
 * This service allows adding, removing, and listing favorite colleges for a user.
 */
class FavoriteCollegeService extends Service
{
    /**
     * Add a college to the user's favorites.
     *
     * @param int $collegeId
     * @return array
     */
    public function addFavorite(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            $college = College::findOrFail($collegeId);

            // Attach the college if not already favorited
            if (!$user->favoriteColleges()->where('college_id', $collegeId)->exists()) {
                $user->favoriteColleges()->attach($collegeId);
                return $this->successResponse('تمت إضافة الكلية إلى المفضلة', 200);
            } else {
                return $this->errorResponse('الكلية مضافة مسبقا', 400);
            }
        } catch (Exception $e) {
            Log::error('Error while adding favorite college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إضافة الكلية إلى المفضلة', 500);
        }
    }

    /**
     * Remove a college from the user's favorites.
     *
     * @param int $collegeId
     * @return array
     */
    public function removeFavorite(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            // Detach the college if it exists in favorites
            if ($user->favoriteColleges()->where('college_id', $collegeId)->exists()) {
                $user->favoriteColleges()->detach($collegeId);
                return $this->successResponse('تمت إزالة الكلية من المفضلة', 200);
            } else {
                return $this->errorResponse('الكلية غير مضافة مسبقا', 400);
            }
        } catch (Exception $e) {
            Log::error('Error while removing favorite college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إزالة الكلية من المفضلة', 500);
        }
    }

    /**
     * Get all favorite colleges for the current user.
     *
     * @return array
     */
    public function getFavorites()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return $this->errorResponse('المستخدم غير مسجل الدخول', 401);
            }

            $favorites = $user->favoriteColleges()->with(['university', 'departments'])->get();

            return $this->successResponse('تم جلب الكليات المفضلة بنجاح', 200, $favorites);
        } catch (Exception $e) {
            Log::error('Error while fetching favorite colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات المفضلة', 500);
        }
    }
}

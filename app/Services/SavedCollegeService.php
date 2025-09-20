<?php

namespace App\Services;

use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for handling user's saved colleges with caching.
 */
class SavedCollegeService extends Service
{
    /**
     * Generate cache key for a specific user.
     */
    protected function getCacheKey($userId)
    {
        return "user_saved_colleges_{$userId}";
    }

    /**
     * Add a college to the authenticated user's saved list.
     */
    public function addSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $college = College::findOrFail($collegeId);

            if (!$user->savedColleges()->where('college_id', $collegeId)->exists()) {
                $priority = ($user->savedColleges()->max('priority') ?? 0) + 1;
                $user->savedColleges()->attach($collegeId, ['priority' => $priority]);

                // تحديث كاش المستخدم فقط
                $this->refreshUserCache($user->id);

                return $this->successResponse('تم حفظ الكلية بنجاح', 200);
            }

            return $this->errorResponse('الكلية محفوظة مسبقاً', 400);

        } catch (Exception $e) {
            Log::error('Error while saving college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حفظ الكلية', 500);
        }
    }

    /**
     * Remove a college from the authenticated user's saved list.
     */
    public function removeSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user->savedColleges()->where('college_id', $collegeId)->exists()) {
                $user->savedColleges()->detach();

                // تحديث كاش المستخدم فقط
                $this->refreshUserCache($user->id);

                return $this->successResponse('تمت إزالة الكلية من المحفوظات', 200);
            }

            return $this->errorResponse('الكلية غير موجودة في المحفوظات', 400);

        } catch (Exception $e) {
            Log::error('Error while removing saved college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إزالة الكلية من المحفوظات', 500);
        }
    }

    /**
     * Retrieve all saved colleges for the authenticated user (cached).
     */
    public function getSaved()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $cacheKey = $this->getCacheKey($user->id);

            $saved = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user) {
                return $user->savedColleges()
                    ->with([
                        'university:id,name,governorate_id',
                        'departments:id,name',
                        'admissions:id,college_id,year,min_average,min_total,preference_score',
                        'branch',
                        'collegeType'
                    ])
                    ->orderBy('priority')
                    ->get();
            });

            // تحسب is_saved مباشرة من الكاش (كل الكليات محفوظة بالفعل)
            foreach ($saved as $college) {
                $college->is_saved = true;
            }

            return $this->successResponse('تم جلب الكليات المحفوظة بنجاح', 200, $saved);

        } catch (Exception $e) {
            Log::error('Error while fetching saved colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات المحفوظة', 500);
        }
    }

    /**
     * Swap the priorities of two saved colleges for the authenticated user.
     */
    public function swapSavedCollegesPriority(array $data)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $collegeId1 = $data['college_id_1'];
            $collegeId2 = $data['college_id_2'];

            $college1 = $user->savedColleges()->where('college_id', $collegeId1)->first();
            $college2 = $user->savedColleges()->where('college_id', $collegeId2)->first();

            if (!$college1 || !$college2) {
                return $this->errorResponse('إحدى الكليتين غير موجودة ضمن المحفوظات', 400);
            }

            $priority1 = $college1->pivot->priority;
            $priority2 = $college2->pivot->priority;

            $user->savedColleges()->updateExistingPivot($collegeId1, ['priority' => $priority2]);
            $user->savedColleges()->updateExistingPivot($collegeId2, ['priority' => $priority1]);


            $this->refreshUserCache($user->id);

            return $this->successResponse('تم تبديل الكليات المحفوظة بنجاح', 200);

        } catch (Exception $e) {
            Log::error('Error while swapping priorities: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تبديل الكليات المحفوظة', 500);
        }
    }

    /**
     * Refresh the cache for a specific user.
     */
    protected function refreshUserCache($userId)
    {
        $cacheKey = $this->getCacheKey($userId);
        Cache::forget($cacheKey);

        $user = Auth::guard('sanctum')->user();
        if ($user && $user->id === $userId) {
            $saved = $user->savedColleges()
                ->with([
                    'university:id,name,governorate_id',
                    'departments:id,name',
                    'admissions:id,college_id,year,min_average,min_total,preference_score',
                    'branch',
                    'collegeType'
                ])
                ->orderBy('priority')
                ->get();

            Cache::put($cacheKey, $saved, now()->addMinutes(30));
        }
    }
}

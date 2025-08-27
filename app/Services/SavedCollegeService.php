<?php

namespace App\Services;

use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for handling user's saved colleges.
 *
 * This service allows adding, removing, listing, and swapping priorities of saved colleges for a user.
 */
class SavedCollegeService extends Service
{
    /**
     * Add a college to the authenticated user's saved list.
     *
     * @param int $collegeId
     * @return array Response with status and message in Arabic
     */
    public function addSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $college = College::findOrFail($collegeId);

            // Attach the college if not already saved
            if (!$user->savedColleges()->where('college_id', $collegeId)->exists()) {

                // Get the highest existing priority or 0 if no items exist
                $lastPriority = $user->savedColleges()->max('priority') ?? 0;

                // Increment priority by 1 for the new item
                $priority = $lastPriority + 1;

                $user->savedColleges()->attach($collegeId, ['priority' => $priority]);

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
     * @return array Response with status and message in Arabic
     */
    public function removeSaved(int $collegeId)
    {
        try {
            $user = Auth::guard('sanctum')->user();

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
     * @return array Response with status, message in Arabic, and data
     */
    public function getSaved()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $saved = $user->savedColleges()
                ->with([
                    'university:id,name,governorate_id',
                    'departments:id,name',
                    'admissions:id,college_id,year,min_average,min_total,preference_score',
                    "branch",
                ])

                ->orderBy('saved_college_user.priority', 'desc') // ترتيب حسب الأولوية من الأعلى إلى الأدنى
                ->get();


            return $this->successResponse('تم جلب الكليات المحفوظة بنجاح', 200, $saved);
        } catch (Exception $e) {
            Log::error('Error while fetching saved colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات المحفوظة', 500);
        }
    }

    /**
     * Swap the priorities of two saved colleges for the authenticated user.
     *
     * @param array $data Contains 'college_id_1' and 'college_id_2'
     * @return array Response with status and message in Arabic
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

            // Swap the priority values
            $priority1 = $college1->pivot->priority;
            $priority2 = $college2->pivot->priority;

            $user->savedColleges()->updateExistingPivot($collegeId1, ['priority' => $priority2]);
            $user->savedColleges()->updateExistingPivot($collegeId2, ['priority' => $priority1]);

            return $this->successResponse('تم تبديل الكليات المحفوظة بنجاح', 200);
        } catch (\Exception $e) {
            Log::error('Error while swapping priorities: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تبديل الكليات المحفوظة', 500);
        }
    }
}

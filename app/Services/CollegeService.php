<?php

namespace App\Services;

use App\Models\College;
use App\Models\CollegeType;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class CollegeService
 *
 * Service responsible for managing colleges.
 * Handles fetching, updating, and deleting colleges with proper logging and error handling.
 */
class CollegeService extends Service
{
    /**
     * Fetch all colleges.
     *
     * Authenticated users receive a paginated list with optional filters applied.
     * Guests receive a random subset of 4 colleges.
     *
     * @param array|null $filteringData Optional filtering parameters
     * @return array JSON response with status, message, and data
     */
    public function getAllColleges(?array $filteringData = [])
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user && $user->is_active == 1) {
                $cacheKey = 'colleges_all_' . md5(json_encode($filteringData));


                $allKeys = Cache::get('all_colleges_keys', []);
                if (!in_array($cacheKey, $allKeys)) {
                    $allKeys[] = $cacheKey;
                    Cache::forever('all_colleges_keys', $allKeys);
                }

                $colleges = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filteringData) {
                    return College::with(['university', 'collegeType', 'departments', 'admissions'])
                        ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                        ->paginate(10);
                });

                $colleges->getCollection()->transform(function ($college) use ($user) {
                    $college->is_saved = $college->savedByUsers()->where('user_id', $user->id)->exists();
                    return $college;
                });
            } else {

                $colleges = College::with(['university', 'departments', 'admissions'])
                    ->inRandomOrder()
                    ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                    ->limit(4)
                    ->get();
            }

            return $this->successResponse('تم جلب الكليات بنجاح.', 200, $colleges);
        } catch (Exception $e) {
            Log::error('Error fetching colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function getNewColleges(?array $filteringData = [])
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user && $user->is_active == 1) {
                $cacheKey = 'new_colleges_' . md5(json_encode($filteringData));


                $allKeys = Cache::get('all_colleges_keys', []);
                if (!in_array($cacheKey, $allKeys)) {
                    $allKeys[] = $cacheKey;
                    Cache::forever('all_colleges_keys', $allKeys);
                }

                $colleges = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filteringData) {
                    return College::with(['university', 'collegeType', 'departments', 'admissions'])
                        ->whereDoesntHave('admissions')
                        ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                        ->get();
                });

                $colleges->transform(function ($college) use ($user) {
                    $college->is_saved = $college->savedByUsers()->where('user_id', $user->id)->exists();
                    return $college;
                });
            } else {

                $colleges = College::with(['university', 'departments', 'admissions'])
                    ->inRandomOrder()
                    ->whereDoesntHave('admissions')
                    ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                    ->limit(4)
                    ->get();
            }

            return $this->successResponse('تم جلب الكليات الجديدة بنجاح.', 200, $colleges);
        } catch (Exception $e) {
            Log::error('Error fetching new colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات الجديدة. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Update a college and its related departments.
     *
     * @param int $id College ID
     * @param array $data Validated data for update
     * @return array JSON response with status and message
     */

    public function updateCollege(int $id, array $data)
    {
        try {
            $college = College::findOrFail($id);

            $college->update([
                'name' => $data['name'] ?? $college->name,
                'university_id' => $data['university_id'] ?? $college->university_id,
                'college_type_id' => $data['college_type_id'] ?? $college->college_type_id,
                'study_duration' => $data['study_duration'] ?? $college->study_duration,
                'gender' => $data['gender'] ?? $college->gender,
                'branch_id' => $data['branch_id'] ?? $college->branch_id,
            ]);


            if (isset($data['departments'])) {
                $college->departments()->sync($data['departments']);
            }


            if (isset($data['admissions']) && is_array($data['admissions'])) {
                foreach ($data['admissions'] as $admData) {
                    if (isset($admData['id'])) {
                        $admission = $college->admissions()->find($admData['id']);
                        if ($admission) {
                            $admission->update([
                                'year' => $admData['year'] ?? $admission->year,
                                'min_average' => $admData['min_average'] ?? $admission->min_average,
                                'min_total' => $admData['min_total'] ?? $admission->min_total,
                                'preference_score' => $admData['preference_score'] ?? $admission->preference_score,
                            ]);
                        }
                    } else {
                        $college->admissions()->create([
                            'year' => $admData['year'],
                            'min_average' => $admData['min_average'],
                            'min_total' => $admData['min_total'],
                            'preference_score' => $admData['preference_score'],
                        ]);
                    }
                }
            }

            return $this->successResponse('تم تحديث الكلية بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('خطأ أثناء تحديث الكلية: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الكلية. يرجى المحاولة مرة أخرى.', 500);
        }
    }


    /**
     * Delete a college and detach its related departments.
     *
     * @param int $id College ID
     * @return array JSON response with status and message
     */
    public function deleteCollege(int $id)
    {
        try {
            $college = College::findOrFail($id);

            // Detach all departments to avoid foreign key constraint issues
            $college->departments()->detach();

            // Delete the college
            $college->delete();

            return $this->successResponse('تم حذف الكلية بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('Error deleting college: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الكلية. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

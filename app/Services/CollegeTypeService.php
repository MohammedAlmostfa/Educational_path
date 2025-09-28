<?php

namespace App\Services;

use App\Models\CollegeType;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CollegeTypeService extends Service
{
    public function getCollegeTypes()
    {
        try {
            // Cache key
            $cacheKey = 'college_types';

            // Get from cache or store if not exists (2 hours)
            $collegeTypes = Cache::remember($cacheKey, now()->addHours(2), function () {
                return CollegeType::select('id', 'name')->get();
            });

            return $this->successResponse(
                'تم استرجاع أنواع الكليات بنجاح.',
                200,
                $collegeTypes
            );
        } catch (Exception $e) {
            Log::error('Error while fetching college types: ' . $e->getMessage());

            return $this->errorResponse(
                'حدث خطأ أثناء استرجاع أنواع الكليات. يرجى المحاولة مرة أخرى.',
                500
            );
        }
    }

}

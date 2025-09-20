<?php

namespace App\Services;

use App\Models\University;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class UniversityService
 *
 * Service responsible for managing universities.
 */
class UniversityService extends Service
{
    /**
     * Retrieve all universities with caching.
     *
     * @return array JSON response with status, message, and data
     */
    public function getAllUniversities()
    {
        try {

            $cacheKey = 'universities_all';


            $universities = Cache::remember($cacheKey, now()->addHours(2), function () {
                return University::select('id', 'name')->get();
            });

            return $this->successResponse('تم استرجاع الجامعات بنجاح.', 200, $universities);

        } catch (Exception $e) {
            Log::error('Error while fetching universities: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء استرجاع الجامعات. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

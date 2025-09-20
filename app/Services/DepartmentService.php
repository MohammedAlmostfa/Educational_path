<?php

namespace App\Services;

use App\Models\Department;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DepartmentService extends Service
{
    /**
     * Get all normal departments (type=0) with caching.
     */
    public function getAllDepartments()
    {
        try {
            // مفتاح الكاش
            $cacheKey = 'all_departments';


            $departments = Cache::remember($cacheKey, now()->addHours(2), function () {
                return Department::select('id', 'name')->get();
            });

            return $this->successResponse('تم استرجاع الأقسام بنجاح.', 200, $departments);
        } catch (Exception $e) {
            Log::error('Error while fetching departments: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء استرجاع الأقسام. يرجى المحاولة مرة أخرى.', 500);
        }
    }

}

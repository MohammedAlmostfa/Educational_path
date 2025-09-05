<?php

namespace App\Services;

use App\Models\Department;
use Exception;
use Illuminate\Support\Facades\Log;

class DepartmentService extends Service
{
    /**
     * Get all normal departments (type=0)
     */
    public function getAllDepartments()
    {
        try {
            $departments = Department::select('id', 'name')

                ->get();

            return $this->successResponse('تم استرجاع الأقسام بنجاح.', 200, $departments);
        } catch (Exception $e) {
            Log::error('Error while fetching departments: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء استرجاع الأقسام. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Get all departments + college types (mixed)
     */
    public function getAllDepartmentsAndCollege(?array $filteringData = null)
    {
        try {
            $departmentsMixed = Department::select('id', 'name', 'type')
                ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                ->where('type', 0)
                ->get();

            return $this->successResponse(
                'تم استرجاع جميع الأقسام وأنواع الكليات بنجاح.',
                200,
                $departmentsMixed
            );
        } catch (Exception $e) {
            Log::error('Error while fetching departments mixed: ' . $e->getMessage());
            return $this->errorResponse(
                'حدث خطأ أثناء استرجاع الأقسام وأنواع الكليات. يرجى المحاولة مرة أخرى.',
                500
            );
        }
    }
}

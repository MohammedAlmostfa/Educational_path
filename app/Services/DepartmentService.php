<?php

namespace App\Services;

use App\Models\Department;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class DepartmentService
 *
 * Service responsible for managing departments.
 * Provides methods to retrieve department data.
 */
class DepartmentService extends Service
{
    /**
     * Retrieve all departments, optionally filtered.
     *
     * @param array|null $filteringData Optional filtering parameters
     * @return array JSON response with status, message, and data
     */
    public function getAllDepartments(?array $filteringData )
    {
        try {
            $departments = Department::select('id', 'name')
                ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                ->get();



            return $this->successResponse('تم استرجاع الأقسام بنجاح.', 200, $departments);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Error while fetching departments: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع الأقسام. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

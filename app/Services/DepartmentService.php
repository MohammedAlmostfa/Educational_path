<?php

namespace App\Services;

use App\Models\Department;
use Exception;
use Illuminate\Support\Facades\Log;

class DepartmentService extends Service
{
    public function getAllDepartments()
    {
        try {
            $departments = Department::select('id','name')->get();

            return $this->successResponse('تم استرجاع الأقسام بنجاح.', 200, $departments);

        } catch (Exception $e) {
            // تسجيل الخطأ للـ debugging
            Log::error('Error while fetching departments: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع الأقسام. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

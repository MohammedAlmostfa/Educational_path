<?php

namespace App\Services;

use App\Models\CollegeType;
use App\Models\Department;
use Exception;
use Illuminate\Support\Facades\Log;

class CollegeTypeService extends Service
{
    public function getCollegeTypes()
    {
        try {

            $collegeTypes = CollegeType::select('id', 'name')

                ->get();

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

<?php 

namespace App\Services;

use App\Models\College;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Log;

class CollegeService extends Service
{
    /**
     * Get all colleges with related university, departments, and admissions.
     *
     * @return array Standardized response
     */
    public function getAllColleges()
    {
        try {
        
            $colleges = College::with(['university', 'departments', 'admissions'])->paginate(10);
            
            return $this->successResponse('تم جلب الكليات بنجاح', 200, $colleges);
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء جلب الكليات: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة الكليات، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

<?php

namespace App\Services;

use App\Models\College;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CollegeService extends Service
{
    public function getAllColleges($filteringData)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                $colleges = College::with(['university', 'departments', 'admissions'])
                 ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                    ->paginate(10);
            } else {
                $colleges = College::with(['university', 'departments', 'admissions'])
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
            }

            return $this->successResponse('تم جلب الكليات بنجاح', 200, $colleges);
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء جلب الكليات: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء معالجة الكليات، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

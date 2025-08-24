<?php

namespace App\Services;

use App\Models\Governorate;
use Exception;
use Illuminate\Support\Facades\Log;

class GovernorateService extends Service
{
    public function getAllGovernorates()
    {
        try {
            $governorates = Governorate::select('id', 'name')->get();

        
            return $this->successResponse('تم استرجاع المحافظات بنجاح.', 200, $governorates);

        } catch (Exception $e) {
            // تسجيل الخطأ للـ debugging
            Log::error('Error while fetching governorates: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع المحافظات. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

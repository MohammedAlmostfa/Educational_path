<?php

namespace App\Services;

use App\Models\Governorate;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service responsible for managing governorates.
 * Handles fetching all governorates from the database.
 */
class GovernorateService extends Service
{
    /**
     * Get all governorates.
     *
     * @return array Response with status, message, and data
     */
    public function getAllGovernorates()
    {
        try {
            // Fetch all governorates (id and name only)
            $governorates = Governorate::select('id', 'name')->get();

            return $this->successResponse('تم استرجاع المحافظات بنجاح.', 200, $governorates);

        } catch (Exception $e) {
            // Log error for debugging
            Log::error('Error while fetching governorates: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع المحافظات. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

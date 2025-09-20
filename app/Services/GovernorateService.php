<?php

namespace App\Services;

use App\Models\Governorate;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service responsible for managing governorates.
 * Handles fetching all governorates from the database.
 */
class GovernorateService extends Service
{
    /**
     * Get all governorates with caching.
     *
     * @return array Response with status, message, and data
     */
    public function getAllGovernorates()
    {
        try {
            // Cache key
            $cacheKey = 'governorates_all';

            // Get from cache or store if not exists (2 hours)
            $governorates = Cache::remember($cacheKey, now()->addHours(2), function () {
                return Governorate::select('id', 'name')->get();
            });

            return $this->successResponse('تم استرجاع المحافظات بنجاح.', 200, $governorates);

        } catch (Exception $e) {
            // Log error for debugging
            Log::error('Error while fetching governorates: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع المحافظات. يرجى المحاولة مرة أخرى.', 500);
        }
    }


}

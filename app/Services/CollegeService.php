<?php

namespace App\Services;

use App\Models\College;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class CollegeService
 *
 * Service responsible for retrieving colleges.
 * Handles fetching colleges with optional filtering and user-based behavior.
 */
class CollegeService extends Service
{
    /**
     * Get all colleges.
     *
     * - Authenticated users get paginated list with filters applied.
     * - Guests get a random subset of colleges (limited to 4).
     *
     * @param array $filteringData Optional filtering parameters
     * @return array Response with status, message (Arabic), and data
     */
    public function getAllColleges($filteringData)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                // Authenticated user: fetch colleges with related university, departments, admissions
                // Apply filters if provided, and paginate the results
                $colleges = College::with(['university', 'department', 'admissions'])
                    ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                    ->paginate(10);
            } else {
                // Guest user: return a random subset of 4 colleges with related data
                $colleges = College::with(['university', 'department', 'admissions'])
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
            }

            return $this->successResponse('تم جلب الكليات بنجاح.', 200, $colleges);
        } catch (Exception $e) {
            Log::error('Error fetching colleges: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء جلب الكليات. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}

<?php

namespace App\Services;

use App\Models\College;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class CollegeService
 *
 * Service responsible for managing colleges.
 * Handles fetching, updating, and deleting colleges with proper logging and error handling.
 */
class CollegeService extends Service
{
    /**
     * Fetch all colleges.
     *
     * Authenticated users receive a paginated list with optional filters applied.
     * Guests receive a random subset of 4 colleges.
     *
     * @param array|null $filteringData Optional filtering parameters
     * @return array JSON response with status, message, and data
     */
    public function getAllColleges(?array $filteringData = [])
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user && $user->is_active == 1) {
                // Authenticated user: fetch colleges with relationships and filters
                $colleges = College::with(['university', 'departments', 'admissions'])
                    ->when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                    ->paginate(10);
            } else {
                // Guest user: return random 4 colleges with relationships
                $colleges = College::with(['university', 'departments', 'admissions'])
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
            }

            return $this->successResponse('Colleges fetched successfully.', 200, $colleges);
        } catch (Exception $e) {
            Log::error('Error fetching colleges: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch colleges. Please try again.', 500);
        }
    }

    /**
     * Update a college and its related departments.
     *
     * @param int $id College ID
     * @param array $data Validated data for update
     * @return array JSON response with status and message
     */
    public function updateCollege(int $id, array $data)
    {
        try {
            $college = College::findOrFail($id);

            // Update basic college fields
            $college->update([
                'name' => $data['name'] ?? $college->name,
                'university_id' => $data['university_id'] ?? $college->university_id,
                'college_type' => $data['college_type'] ?? $college->college_type,
                'study_duration' => $data['study_duration'] ?? $college->study_duration,
                'gender' => $data['gender'] ?? $college->gender,
                'branch_id' => $data['branch_id'] ?? $college->branch_id,
            ]);

            // Update related departments if provided
            if (isset($data['departments'])) {
                $college->departments()->sync($data['departments']);
            }

            return $this->successResponse('College updated successfully.', 200);
        } catch (Exception $e) {
            Log::error('Error updating college: ' . $e->getMessage());
            return $this->errorResponse('Failed to update college. Please try again.', 500);
        }
    }

    /**
     * Delete a college and detach its related departments.
     *
     * @param int $id College ID
     * @return array JSON response with status and message
     */
    public function deleteCollege(int $id)
    {
        try {
            $college = College::findOrFail($id);

            // Detach all departments to avoid foreign key constraint issues
            $college->departments()->detach();

            // Delete the college
            $college->delete();

            return $this->successResponse('College deleted successfully.', 200);
        } catch (Exception $e) {
            Log::error('Error deleting college: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete college. Please try again.', 500);
        }
    }
}

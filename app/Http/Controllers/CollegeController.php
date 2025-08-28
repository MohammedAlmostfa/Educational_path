<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CollegeService;
use App\Http\Requests\UpdateCollege;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CollegeResource;

/**
 * Class CollegeController
 *
 * Handles all college-related HTTP requests.
 * Provides endpoints for:
 * - Fetching colleges (paginated for authenticated users, full collection for guests)
 * - Updating a college
 * - Deleting a college
 */
class CollegeController extends Controller
{
    /**
     * @var CollegeService
     * Service instance for handling college operations
     */
    protected $collegeService;

    /**
     * Constructor to inject CollegeService
     *
     * @param CollegeService $collegeService
     */
    public function __construct(CollegeService $collegeService)
    {
        $this->collegeService = $collegeService;
    }

    /**
     * Display a listing of colleges.
     *
     * - Authenticated users receive a paginated collection with filters applied.
     * - Guests receive a limited collection (randomized).
     *
     * @param Request $request The HTTP request containing optional filters or query parameters
     * @return \Illuminate\Http\JsonResponse JSON response with colleges and status
     */
    public function index(Request $request)
    {
        // Call the service to get colleges, passing any filter/query parameters
        $result = $this->collegeService->getAllColleges($request->all());

        // Get currently authenticated user (if any)
        $user = Auth::guard('sanctum')->user();

        if ($user && $user->is_active == 1) {
            // Authenticated users: return paginated response
            return $result['status'] === 200
                ? self::paginated(
                    $result['data'],
                    CollegeResource::class,
                    $result['message'],
                    $result['status']
                )
                : self::error(null, $result['message'], $result['status']);
        } else {
            // Guests: return full collection without pagination
            return $result['status'] === 200
                ? self::success(
                    CollegeResource::collection($result['data']),
                    $result['message'],
                    $result['status']
                )
                : self::error(null, $result['message'], $result['status']);
        }
    }

    /**
     * Update a specific college.
     *
     * Uses the UpdateCollege FormRequest to validate input data.
     *
     * @param UpdateCollege $request Validated request data
     * @param int $id ID of the college to update
     * @return \Illuminate\Http\JsonResponse JSON response with update status and message
     */
    public function update(UpdateCollege $request, $id)
    {
        // Get validated data from the request
        $data = $request->validated();

        // Call service to update the college
        $result = $this->collegeService->updateCollege($id, $data);

        // Return standardized JSON response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Delete a specific college.
     *
     * Calls the service to remove the college and its related relationships if needed.
     *
     * @param int $id ID of the college to delete
     * @return \Illuminate\Http\JsonResponse JSON response with deletion status and message
     */
    public function delete($id)
    {
        // Call service to delete the college
        $result = $this->collegeService->deleteCollege($id);

        // Return standardized JSON response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

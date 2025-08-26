<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollegeResource;
use App\Services\CollegeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Class CollegeController
 *
 * Controller responsible for handling college-related requests.
 * Provides endpoints to fetch colleges, either paginated for authenticated users
 * or full collection for guests.
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
     * Returns paginated data if user is authenticated via Sanctum,
     * otherwise returns full collection.
     *
     * @param Request $request The HTTP request containing optional filters or query parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve all colleges from the service with optional request parameters
        $result = $this->collegeService->getAllColleges($request->all());

        // Get currently authenticated user (if any)
        $user = Auth::guard('sanctum')->user();

        if ($user && $user->is_active == 1) {

            // Authenticated users get paginated response
            return $result['status'] === 200
                ? self::paginated(
                    $result['data'],
                    CollegeResource::class,
                    $result['message'],
                    $result['status']
                )
                : self::error(null, $result['message'], $result['status']);
        } else {
            // Guests get full collection
            return $result['status'] === 200
                ? self::success(
                    CollegeResource::collection($result['data']),
                    $result['message'],
                    $result['status']
                )
                : self::error(null, $result['message'], $result['status']);
        }
    }
}

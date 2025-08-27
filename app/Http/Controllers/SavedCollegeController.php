<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SavedCollegeService;
use App\Http\Resources\CollegeResource;
use App\Http\Requests\SwapSavedCollegesRequest;

/**
 * Class SavedCollegeController
 *
 * Controller responsible for managing user's saved colleges.
 * Handles adding, removing, retrieving, and swapping priorities of saved colleges.
 */
class SavedCollegeController extends Controller
{
    /**
     * @var SavedCollegeService
     * Service instance for handling saved college operations
     */
    protected $savedCollegeService;

    /**
     * Constructor to inject SavedCollegeService
     *
     * @param SavedCollegeService $savedCollegeService
     */
    public function __construct(SavedCollegeService $savedCollegeService)
    {
        $this->savedCollegeService = $savedCollegeService;
    }

    /**
     * Add a college to the user's saved list.
     *
     * @param int $collegeId College ID to be saved
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSaved($collegeId)
    {
        // Call service to add the college to user's saved list
        $result = $this->savedCollegeService->addSaved($collegeId);

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove a college from the user's saved list.
     *
     * @param int $collegeId College ID to be removed
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSaved($collegeId)
    {
        // Call service to remove the college from user's saved list
        $result = $this->savedCollegeService->removeSaved($collegeId);

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Get all saved colleges for the current authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaved()
    {
        // Call service to retrieve saved colleges
        $result = $this->savedCollegeService->getSaved();

        // Return success response with saved colleges or error
        return $result['status'] === 200
            ? self::success(
                    CollegeResource::collection($result['data']),
                    $result['message'],
                    $result['status']
                )
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Swap priorities between two saved colleges for the authenticated user.
     *
     * @param SwapSavedCollegesRequest $request Request validated with college IDs to swap
     * @return \Illuminate\Http\JsonResponse
     */
    public function swapSavedColleges(SwapSavedCollegesRequest $request)
    {
        // Validate the incoming request to ensure both college IDs are provided
        $data = $request->validated();

        // Call service to swap the priorities of the two saved colleges
        $result = $this->savedCollegeService->swapSavedCollegesPriority($data);

        // Return success or error response based on service result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

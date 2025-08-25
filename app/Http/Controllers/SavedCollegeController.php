<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SavedCollegeService;

/**
 * Class SavedCollegeController
 *
 * Controller responsible for managing user's saved colleges.
 * Handles adding, removing, and retrieving saved colleges.
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
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

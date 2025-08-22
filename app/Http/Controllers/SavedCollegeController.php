<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SavedCollegeService;

class SavedCollegeController extends Controller
{
    protected $savedCollegeService;

    public function __construct(SavedCollegeService $savedCollegeService)
    {
        $this->savedCollegeService = $savedCollegeService;
    }

    /**
     * Add a college to the user's saved list.
     *
     * @param int $collegeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSaved($collegeId)
    {
        $result = $this->savedCollegeService->addSaved($collegeId);

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove a college from the user's saved list.
     *
     * @param int $collegeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSaved($collegeId)
    {
        $result = $this->savedCollegeService->removeSaved($collegeId);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Get all saved colleges for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaved()
    {
        $result = $this->savedCollegeService->getSaved();

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

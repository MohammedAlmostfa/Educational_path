<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteCollegeService;

class FavoriteCollegeController extends Controller
{
    protected $favoriteCollegeService;

    public function __construct(FavoriteCollegeService $favoriteCollegeService)
    {
        $this->favoriteCollegeService = $favoriteCollegeService;
    }

    /**
     * Add a college to the user's favorites.
     *
     * @param int $collegeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFavorite($collegeId)
    {
        $result = $this->favoriteCollegeService->addFavorite($collegeId);

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove a college from the user's favorites.
     *
     * @param int $collegeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFavorite($collegeId)
    {
        $result = $this->favoriteCollegeService->removeFavorite($collegeId);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Get all favorite colleges for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFavorites()
    {
        $result = $this->favoriteCollegeService->getFavorites();

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\CollegeTypeService;

/**
 * Class CollegeTypeController
 *
 * This controller handles requests related to College Types.
 * It communicates with the CollegeTypeService to fetch, process,
 * and return college type data.
 *
 * @package App\Http\Controllers
 */
class CollegeTypeController extends Controller
{
    /**
     * @var CollegeTypeService
     */
    protected $collegeTypeService;

    /**
     * CollegeTypeController constructor.
     *
     * @param CollegeTypeService $collegeTypeService
     */
    public function __construct(CollegeTypeService $collegeTypeService)
    {
        // Inject the CollegeTypeService dependency
        $this->collegeTypeService = $collegeTypeService;
    }

    /**
     * Display a listing of the college types.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function index()
    {
        // Get college types from the service
        $result = $this->collegeTypeService->getCollegeTypes();

        // Return a standardized success/error response
        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

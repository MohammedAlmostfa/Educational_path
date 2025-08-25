<?php

namespace App\Http\Controllers;

use App\Services\GovernorateService;
use Illuminate\Http\Request;

/**
 * Class GovernorateController
 *
 * Controller responsible for handling governorate-related requests.
 * Provides an endpoint to fetch all governorates.
 */
class GovernorateController extends Controller
{
    /**
     * @var GovernorateService
     * Service instance for governorate operations
     */
    protected $governorateService;

    /**
     * Constructor to inject GovernorateService
     *
     * @param GovernorateService $governorateService
     */
    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }

    /**
     * Display a listing of all governorates.
     *
     * Calls the GovernorateService to retrieve all governorates
     * and returns a standardized success or error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Fetch all governorates using the service
        $result = $this->governorateService->getAllGovernorates();

        // Return success response if status is 200, otherwise return error
        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

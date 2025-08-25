<?php

namespace App\Http\Controllers;

use App\Services\DepartmentService;
use Illuminate\Http\Request;

/**
 * Class DepartmentController
 *
 * Controller responsible for handling department-related requests.
 * Uses the DepartmentService to fetch department data.
 */
class DepartmentController extends Controller
{
    /**
     * @var DepartmentService
     * Service instance for department operations
     */
    protected $departmentService;

    /**
     * DepartmentController constructor.
     *
     * @param DepartmentService $departmentService Injects the department service
     */
    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display a listing of all departments.
     *
     * Calls the DepartmentService to retrieve all departments
     * and returns a standardized success or error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Call service to get all departments
        $result = $this->departmentService->getAllDepartments();

        // Return success response if status is 200, otherwise return error response
        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Requests\DepartmentFilterRequest;


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
     */
    protected $departmentService;

    /**
     * Constructor injects the department service.
     *
     * @param DepartmentService $departmentService
     */
    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display a listing of all departments.
     *
     * Supports optional filtering via request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(DepartmentFilterRequest $request)
    {
        $filteringData = $request->validated();
        $result = $this->departmentService->getAllDepartments($filteringData);

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

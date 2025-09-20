<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Requests\DepartmentFilterRequest;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Get all normal departments (type=0)
     */
    public function index()
    {
        $result = $this->departmentService->getAllDepartments();

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    // /**
    //  * Get all departments + college types (mixed)
    //  */
    // public function getAllDepartmentsAndCollege(DepartmentFilterRequest $request)
    // {
    //     $filteringData = $request->validated();
    //     $result = $this->departmentService->getAllDepartmentsAndCollege($filteringData);

    //     return $result['status'] === 200
    //         ? self::success($result['data'] ?? null, $result['message'], $result['status'])
    //         : self::error(null, $result['message'], $result['status']);
    // }
}

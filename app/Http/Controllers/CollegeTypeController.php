<?php

namespace App\Http\Controllers;

use App\Services\CollegeTypeService;

class CollegeTypeController extends Controller
{
    protected $collegeTypeService;

    public function __construct(CollegeTypeService $collegeTypeService)
    {
        $this->collegeTypeService = $collegeTypeService;
    }

    public function index()
    {
        return $this->collegeTypeService->getCollegeTypes();
        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

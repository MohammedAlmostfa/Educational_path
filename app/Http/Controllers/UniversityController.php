<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UniversityService;
use App\Http\Requests\UniversityFilterRequest; // إذا أضفت FormRequest للفلترة

class UniversityController extends Controller
{
    /**
     * @var UniversityService
     */
    protected $universityService;

    /**
     * Constructor injects the university service.
     *
     * @param UniversityService $universityService
     */
    public function __construct(UniversityService $universityService)
    {
        $this->universityService = $universityService;
    }

    /**
     * Display a listing of all universities.
     *
     * Supports optional filtering via request parameters.
     *
     * @param UniversityFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $result = $this->universityService->getAllUniversities();

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

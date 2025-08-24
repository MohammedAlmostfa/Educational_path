<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollegeResource;
use App\Services\CollegeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class CollegeController extends Controller
{
    protected $collegeService;

    public function __construct(CollegeService $collegeService)
    {
        $this->collegeService = $collegeService;
    }

    public function index(Request $request)
{
    $result = $this->collegeService->getAllColleges($request->all());
    $user = Auth::guard('sanctum')->user();

    if ($user) {
        return $result['status'] === 200
            ? self::paginated(
                $result['data'],
                CollegeResource::class,
                $result['message'],
                $result['status']
            )
            : self::error(null, $result['message'], $result['status']);
    } else {
        return $result['status'] === 200
            ? self::success(
                CollegeResource::collection($result['data']),
                $result['message'],
                $result['status']
            )
            : self::error(null, $result['message'], $result['status']);
    }
}

    
}

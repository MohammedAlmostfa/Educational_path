<?php

namespace App\Http\Controllers;
use App\Http\Resources\CollegeResource;
use App\Services\CollegeService;
use Illuminate\Http\Request;


class CollegeController extends Controller
{
        protected $collegeService;
    public function __construct(CollegeService $collegeService)
    {
        $this->collegeService = $collegeService;
    }

    public function index(){

   $result = $this->collegeService->getAllColleges(  );


        return $result['status'] === 200
            ? self::paginated($result['data'], CollegeResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

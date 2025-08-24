<?php

namespace App\Http\Controllers;

use App\Services\GovernorateService;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    protected $governorateService;

    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }

    public function index()
    {
        $result = $this->governorateService->getAllGovernorates();

        return $result['status'] === 200
            ? self::success($result['data'] ?? null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

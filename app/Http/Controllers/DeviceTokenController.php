<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatOrUpdateDeviceToken;
use App\Models\DeviceToken;
use App\Services\DeviceService;
use App\Services\DeviceTokenService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeviceTokenController extends Controller
{

    protected $deviceTokenService;
    public function __construct(DeviceTokenService $deviceTokenService)
    {
        $this->deviceTokenService = $deviceTokenService;
    }
    public function createOrUpdate(CreatOrUpdateDeviceToken $request){

   $validatedData = $request->validated(); 
   $this->deviceTokenService->createOrUpdate($validatedData);

    }
}

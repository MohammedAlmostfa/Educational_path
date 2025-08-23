<?php

namespace App\Http\Controllers;


use App\Http\Requests\User\CreateOrUpdateDeviceToken;
use App\Services\DeviceTokenService;

/**
 * Controller for managing device tokens.
 * 
 * This controller handles creating or updating device tokens for users,
 * typically used for push notifications or mobile authentication.
 */
class DeviceTokenController extends Controller
{
    /**
     * The service responsible for device token operations.
     *
     * @var DeviceTokenService
     */
    protected $deviceTokenService;

    /**
     * DeviceTokenController constructor.
     *
     * @param DeviceTokenService $deviceTokenService
     */
    public function __construct(DeviceTokenService $deviceTokenService)
    {
        $this->deviceTokenService = $deviceTokenService;
    }

    /**
     * Create or update a device token for the authenticated user.
     *
     * @param CreateOrUpdateDeviceToken $request Validated request containing token data.
     * @return \Illuminate\Http\JsonResponse Returns a standardized JSON response.
     */
    public function createOrUpdate(CreateOrUpdateDeviceToken $request)
    {
        // Validate request input
        $validatedData = $request->validated();

        // Call the service to create or update the token
        $result = $this->deviceTokenService->createOrUpdate($validatedData);

        // Return a standardized success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

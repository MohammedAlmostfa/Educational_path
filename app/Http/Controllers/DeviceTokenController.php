<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateOrUpdateDeviceToken;
use App\Services\DeviceTokenService;

/**
 * Class DeviceTokenController
 *
 * Controller responsible for managing device tokens for users.
 * Typically used for push notifications or mobile authentication.
 */
class DeviceTokenController extends Controller
{
    /**
     * @var DeviceTokenService
     * Service instance to handle device token operations
     */
    protected $deviceTokenService;

    /**
     * Constructor to inject DeviceTokenService
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
     * Validates the request input and delegates the creation or update
     * process to the DeviceTokenService.
     *
     * @param CreateOrUpdateDeviceToken $request Validated request containing token data
     * @return \Illuminate\Http\JsonResponse Standardized success or error response
     */
    public function createOrUpdate(CreateOrUpdateDeviceToken $request)
    {
        // Validate request input using custom FormRequest
        $validatedData = $request->validated();

        // Call the service to handle create or update operation
        $result = $this->deviceTokenService->createOrUpdate($validatedData);

        // Return standardized JSON response based on service result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

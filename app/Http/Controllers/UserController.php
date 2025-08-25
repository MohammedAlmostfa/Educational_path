<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\checkActivationCodeRequest;
use App\Http\Requests\User\CreateUserDataRequest;
use App\Http\Requests\User\FilterUser;
use App\Http\Requests\User\updateUserDataRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

/**
 * Class UserController
 *
 * Controller responsible for managing users.
 * Handles listing, creating, updating, activating, and fetching authenticated user data.
 */
class UserController extends Controller
{
    /**
     * @var UserService
     * Service instance for handling user-related operations
     */
    protected $userService;

    /**
     * Constructor to inject UserService
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * List users with optional filters.
     *
     * @param FilterUser $request Validated request for filtering users
     * @return \Illuminate\Http\JsonResponse Paginated list of users
     */
    public function index(FilterUser $request)
    {
        // Validate request input
        $validatedData = $request->validated();

        // Retrieve filtered users from service
        $result = $this->userService->getAllUser($validatedData);

        // Return paginated response or error
        return $result['status'] === 200
            ? self::paginated($result['data'], UserResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Create a new user.
     *
     * @param CreateUserDataRequest $request Validated request containing user data
     * @return \Illuminate\Http\JsonResponse
     */
    public function creat(CreateUserDataRequest $request)
    {
        // Validate request data
        $validatedData = $request->validated();

        // Add new user using service
        $result = $this->userService->AddUserData($validatedData);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update existing user data.
     *
     * @param updateUserDataRequest $request Validated request containing updated user data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(updateUserDataRequest $request)
    {
        // Validate request data
        $validatedData = $request->validated();

        // Update user using service
        $result = $this->userService->updateUserData($validatedData);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Activate a user account by ID.
     *
     * @param int $id User ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function active($id)
    {
        // Call service to activate account
        $result = $this->userService->Activaccount($id);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Check the activation code for a user.
     *
     * @param checkActivationCodeRequest $request Validated request containing activation code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkActivationCode(checkActivationCodeRequest $request)
    {
        // Validate request input
        $validatedData = $request->validated();

        // Call service to verify activation code
        $result = $this->userService->checkActivationCode($validatedData);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Get the authenticated user's data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // Retrieve current user data
        $result = $this->userService->getUserData();

        // Return user resource or error
        return $result['status'] === 200
            ? self::success(new UserResource($result['data']), $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

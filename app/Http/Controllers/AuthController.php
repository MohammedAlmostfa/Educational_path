<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Requests\AuthRequest\GoogelloginRequest;
use App\Http\Requests\AuthRequest\LogoutRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Class AuthController
 *
 * Controller responsible for user authentication operations including
 * registration, login, logout, and Google OAuth login.
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     * Service instance for handling authentication logic
     */
    protected $authService;

    /**
     * Constructor to inject AuthService
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     *
     * Validates incoming request data and delegates user registration
     * to the AuthService.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // Validate the request using the custom RegisterRequest
        $validatedData = $request->validated();

        // Call the AuthService to handle registration
        $result = $this->authService->register($validatedData);

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Login a user with email and password
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // Validate login credentials
        $validatedData = $request->validated();

        // Delegate login process to AuthService
        $result = $this->authService->login($validatedData);

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Logout the current authenticated user
     *
     * Calls AuthService to revoke authentication tokens or sessions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Call logout method in AuthService
        $result = $this->authService->logout();

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Login a user using Google OAuth
     *
     * Validates the incoming Google token and delegates login process
     * to AuthService.
     *
     * @param GoogelloginRequest $request The request containing Google token
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithGoogle(GoogelloginRequest $request)
    {
        // Validate the request to ensure Google token is provided
        $validatedData = $request->validated();

        // Authenticate or register user using the provided Google token
        $result = $this->authService->loginWithGoogle($validatedData['googleToken']);

        // Return standardized success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

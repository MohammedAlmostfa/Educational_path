<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Requests\AuthRequest\GoogelloginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function register(RegisterRequest $request)
    {
      $validatedData = $request->validated();
        $result = $this->authService->register($validatedData);
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    public function login(LoginRequest $request)    {
      $validatedData = $request->validated();
        $result = $this->authService->login($validatedData);
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    public function logout()
    {
        $result = $this->authService->login();

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
 /**
     * Login a user using Google OAuth.
     *
     * @param GoogelloginRequest $request The request containing Google access token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginwithGoogel(GoogelloginRequest $request)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Call the AuthService to login the user using Google
        $result = $this->authService->loginWithGoogle($validationData['googleToken']);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

}

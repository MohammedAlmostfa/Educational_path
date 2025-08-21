<?php

namespace App\Http\Controllers;

use App\Http\Requests\checkActivationCodeRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $result = $this->userService->getAllUser();


        return $result['status'] === 200
            ? self::paginated($result['data'], UserResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
  public function update($id)
{
    $result = $this->userService->Activaccount($id);

    return $result['status'] === 200
        ? self::success($result['data'], $result['message'], $result['status'])
        : self::error(null, $result['message'], $result['status']);
}
  public function checkActivationCode(checkActivationCodeRequest $request)
{
    $validatedData = $request->validated();
    $result = $this->userService->checkActivationCode($validatedData );

    return $result['status'] === 200
        ? self::success($result['data'], $result['message'], $result['status'])
        : self::error(null, $result['message'], $result['status']);
}

}

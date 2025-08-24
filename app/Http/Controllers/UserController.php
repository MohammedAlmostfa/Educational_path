<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\checkActivationCodeRequest;
use App\Http\Requests\User\CreateUserDataRequest;
use App\Http\Requests\User\FilterUser;
use App\Http\Resources\UserResource;
use App\Services\UserService;


class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(FilterUser $request)
    {
        $validatedData = $request->validated();
        $result = $this->userService->getAllUser($validatedData);


        return $result['status'] === 200
            ? self::paginated($result['data'], UserResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }


    public function creat(CreateUserDataRequest $request)
    {
        $validatedData = $request->validated();
        $result = $this->userService->AddUserData($validatedData);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
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
        $result = $this->userService->checkActivationCode($validatedData);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    public function me()
    {
        $result = $this->userService->getUserData();

        return $result['status'] === 200
            ? self::success(new UserResource($result['data']), $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}

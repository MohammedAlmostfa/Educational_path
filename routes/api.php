<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('/activation/{id}', [UserController::class, 'update']);


Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']);     
Route::post('/logout', [AuthController::class, 'logout']);    
Route::post('//access-with-google', [AuthController::class, 'loginwithgoogel']);    
Route::post('/save-fcm', [DeviceTokenController::class, 'createOrUpdate']);  


Route::get('/show-unactive-users', [UserController::class, 'index']);  

Route::post('/check-activation_code', [UserController::class, 'checkActivationCode']);  

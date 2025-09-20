<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CollegeTypeController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\SavedCollegeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes for the application.
| Routes are grouped by public, authenticated, admin, and activated user.
|
*/

/*
|--------------------------------------------------------------------------
| Test Authenticated User Route
|--------------------------------------------------------------------------
| Returns the authenticated user's information.
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Registration, login, logout, Google login, and device FCM token management.
*/
Route::post('/register', [AuthController::class, 'register']); // Register a new user
Route::post('/login', [AuthController::class, 'login']); // User login
Route::post('/logout', [AuthController::class, 'logout']); // User logout
Route::post('/access-with-google', [AuthController::class, 'loginWithGoogle']); // Login via Google
Route::post('/save-fcm', [DeviceTokenController::class, 'createOrUpdate']); // Save or update device FCM token

/*
|--------------------------------------------------------------------------
| Public Data Routes
|--------------------------------------------------------------------------
| Accessible without authentication: colleges, new colleges, content, and location data.
*/
Route::get('/get-colleges', [CollegeController::class, 'index']); // List all colleges
Route::get('/new-colleges', [CollegeController::class, 'getNewCollege']); // List new colleges
Route::get('/content', [ContentController::class, 'index']); // Get all content
Route::post('/add-viewers/{id}', [ContentController::class, 'addViewers']); // Increment content viewers
Route::get('/college-types', [CollegeTypeController::class, 'index']); // Get college types
Route::get('/university', [UniversityController::class, 'index']); // Get all universities
Route::get('/governorate', [GovernorateController::class, 'index']); // Get all governorates
Route::get('/department', [DepartmentController::class, 'index']); // Get all departments

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
| Requires authentication via Sanctum.
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/check-activation_code', [UserController::class, 'checkActivationCode']); // Verify activation code
    Route::put('/college/{id}', [CollegeController::class, 'update']); // Update college info
    Route::delete('/college/{id}', [CollegeController::class, 'delete']); // Delete a college
    Route::post('/logout', [AuthController::class, 'logout']); // User logout
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Requires authentication + admin role + user activation.
| Admins manage content, users, and other resources.
*/
Route::middleware(['auth:sanctum', 'admin', 'activation'])->group(function () {
    // Content management
    Route::post('/content', [ContentController::class, 'store']); // Create new content
    Route::post('/content/{id}', [ContentController::class, 'update']); // Update content
    Route::delete('/content/{content}', [ContentController::class, 'destroy']); // Delete content

    // Admin logout
    Route::post('/adminlogout', [UserController::class, 'Userlogout']);

    // User management
    Route::get('/show-unactive-users', [UserController::class, 'index'])->name('show-unactive-users'); // List all inactive users
    // Route::put('/activation/{id}', [UserController::class, 'active']); // Activate a user (commented out)
});

/*
|--------------------------------------------------------------------------
| Authenticated & Activated User Routes
|--------------------------------------------------------------------------
| Requires authentication + user activation.
| Includes content interaction, user info management, and saved colleges.
*/
Route::middleware(['auth:sanctum', 'activation'])->group(function () {
    // Content
    Route::post('/add-viewers/{id}', [ContentController::class, 'addViewers']); // Increment content viewers

    // User info management
    Route::post('/set-user-information', [UserController::class, 'creat']); // Save user info
    Route::post('/update-user-information', [UserController::class, 'update']); // Update user info
    Route::get('/me', [UserController::class, 'me']); // Get current authenticated user info

    // Saved colleges management
    Route::post('/saved/{collegeId}', [SavedCollegeController::class, 'addSaved']); // Add a college to saved list
    Route::delete('/saved/{collegeId}', [SavedCollegeController::class, 'removeSaved']); // Remove a college from saved list
    Route::get('/saved', [SavedCollegeController::class, 'getSaved']); // Get all saved colleges
    Route::post('/swap-saved-collage', [SavedCollegeController::class, 'swapSavedColleges']); // Swap priorities of saved colleges
});

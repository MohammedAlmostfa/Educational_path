<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\SavedCollegeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here we define all API routes for the application.
| Some routes are protected with middleware to check authentication
| or user permissions (e.g., admin or activation check).
|
*/

/*
|--------------------------------------------------------------------------
| Test Authenticated User Route
|--------------------------------------------------------------------------
|
| Route to return the authenticated user's info.
|
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user registration, login, logout, Google login,
| and saving/updating device FCM tokens.
|
*/
Route::post('/register', [AuthController::class, 'register']); // Register a new user
Route::post('/login', [AuthController::class, 'login']); // User login
Route::post('/logout', [AuthController::class, 'logout']); // User logout
Route::post('/access-with-google', [AuthController::class, 'loginWithGoogle']); // Login via Google
Route::post('/save-fcm', [DeviceTokenController::class, 'createOrUpdate']); // Save or update device FCM token

// Public access: Get all colleges
Route::get('/get-colleges', [CollegeController::class, 'index']);

// Public content routes
Route::get('/content', [ContentController::class, 'index']); // Get all content
Route::post('/add-viewers/{id}', [ContentController::class, 'addViewers']); // Increment content viewers

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
|
| Routes that require the user to be logged in (auth:sanctum).
|
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/check-activation_code', [UserController::class, 'checkActivationCode']); // Verify activation code
    Route::put('/college/{id}', [CollegeController::class, 'update']); // Update college info
    Route::delete('college/{id}', [CollegeController::class, 'delete']); // Delete a college
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes that require authentication + admin role + activation check.
| Admins can manage content, users, and universities.
|
*/
Route::middleware(['auth:sanctum', 'admin', 'activation'])->group(function () {
    // Content management
    Route::post('/content', [ContentController::class, 'store']); // Create new content
    Route::post('/content/{id}', [ContentController::class, 'update']); // Update specific content
    Route::delete('/content/{content}', [ContentController::class, 'destroy']); // Delete specific content

    // User management
    Route::get('/show-unactive-users', [UserController::class, 'index']); // List all inactive users
    // Route::put('/activation/{id}', [UserController::class, 'active']); // Activate a specific user (commented out)

    // University management
    Route::get('/university', [UniversityController::class, 'index']); // Get all universities/departments
});

/*
|--------------------------------------------------------------------------
| Authenticated & Activated User Routes
|--------------------------------------------------------------------------
|
| Routes that require authentication + user activation.
| Includes content viewing, location data, user info management, and saved colleges.
|
*/
Route::middleware(['auth:sanctum', 'activation'])->group(function () {
    // Content

    Route::post('/add-viewers/{id}', [ContentController::class, 'addViewers']); // Increment content viewers

    // Location data
    Route::get('/governorate', [GovernorateController::class, 'index']); // Get all governorates
    Route::get('/department', [DepartmentController::class, 'index']); // Get all departments

    // User information management
    Route::post('/set-user-information', [UserController::class, 'creat']); // Save user info
    Route::post('/update-user-information', [UserController::class, 'update']); // Update user info
    Route::get('/me', [UserController::class, 'me']); // Get current authenticated user info

    // Saved Colleges management
    Route::post('/saved/{collegeId}', [SavedCollegeController::class, 'addSaved']); // Add a college to saved list
    Route::delete('/saved/{collegeId}', [SavedCollegeController::class, 'removeSaved']); // Remove a college from saved list
    Route::get('/saved', [SavedCollegeController::class, 'getSaved']); // Get all saved colleges
    Route::post('/swap-saved-collage', [SavedCollegeController::class, 'swapSavedColleges']); // Swap priorities of saved colleges
});

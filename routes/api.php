<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\FavoriteCollegeController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\SavedCollegeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| هنا يتم تعريف كل مسارات الـ API الخاصة بالتطبيق.
| بعض المسارات محمية بميدل وير للتحقق من تسجيل الدخول أو صلاحيات المستخدم.
|
*/

// مسار لتجربة المستخدم المسجل دخول فقط
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// مسارات التسجيل وتسجيل الدخول والخروج
Route::post('/register', [AuthController::class, 'register']); // تسجيل مستخدم جديد
Route::post('/login', [AuthController::class, 'login']); // تسجيل دخول
Route::post('/logout', [AuthController::class, 'logout']); // تسجيل خروج
Route::post('/access-with-google', [AuthController::class, 'loginWithGoogle']); // تسجيل دخول عبر جوجل
Route::post('/save-fcm', [DeviceTokenController::class, 'createOrUpdate']); // حفظ أو تحديث توكن FCM للجهاز
  // جلب قائمة الكليات
    Route::get('/get-colleges', [CollegeController::class, 'index']); 
// مسارات تحتاج تسجيل الدخول فقط
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/check-activation_code', [UserController::class, 'checkActivationCode']); // التحقق من كود التفعيل
});

// مسارات تحتاج صلاحيات الادمن
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // إنشاء محتوى جديد
    Route::post('/content', [ContentController::class, 'store']); 

    // تعديل محتوى معين
    Route::post('/content/{id}', [ContentController::class, 'update']); 

    // حذف محتوى معين
    Route::delete('/content/{content}', [ContentController::class, 'destroy']); 

    // عرض جميع المستخدمين غير المفعلين
    Route::get('/show-unactive-users', [UserController::class, 'index']); 
Route::get('/me', [UserController::class, 'me']); 
    // تفعيل مستخدم محدد
    Route::put('/activation/{id}', [UserController::class, 'active']); 
});

// مسارات تحتاج تسجيل الدخول والتحقق من التفعيل
Route::middleware(['auth:sanctum', 'activation'])->group(function () {
    // عرض المحتوى
    Route::get('/content', [ContentController::class, 'index']); 

    Route::get('/department', [DepartmentController::class, 'index']); 
    Route::get('/governorate', [GovernorateController::class, 'index']); 
    // حفظ معلومات المستخدم
    Route::post('/set-user-information', [UserController::class, 'creat']); 
 Route::post('/update-user-information', [UserController::class, 'update']); 
    // إضافة كلية للمفضلة
    Route::post('/saved/{collegeId}', [SavedCollegeController::class, 'addSaved']); 

    // إزالة كلية من المفضلة
    Route::delete('/saved/{collegeId}', [SavedCollegeController::class, 'removeSaved']); 

  

    // جلب الكليات المحفوظة للمستخدم
    Route::get('/saved', [SavedCollegeController::class, 'getSaved']); 
});

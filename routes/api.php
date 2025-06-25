<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuardController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\GuardAttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RolePermissionController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/roles', [RoleController::class, 'index']);
Route::post('/create-role', [RoleController::class, 'store']);
Route::post('/role/{role}', [RoleController::class, 'update']);
Route::delete('/role/{role}', [RoleController::class, 'destroy']);


// Guards
Route::prefix('guards')->group(function () {
    Route::get('/', [GuardController::class, 'index']);
    Route::post('/{id}', [GuardController::class, 'update']);
    Route::get('/{id}', [GuardController::class, 'show']);
    Route::post('/', [GuardController::class, 'store']);
    Route::delete('/{id}', [GuardController::class, 'destroy']);
});

// Shifts
Route::prefix('shifts')->group(function () {
    Route::get('/', [ShiftController::class, 'index']);
    Route::get('/{id}', [ShiftController::class, 'show']);
    Route::post('/', [ShiftController::class, 'store']);
    Route::put('/{id}', [ShiftController::class, 'update']);
    Route::delete('/{id}', [ShiftController::class, 'destroy']);
});

// Locations
Route::prefix('locations')->group(function () {
    Route::get('/', [LocationController::class, 'index']);
    Route::post('/', [LocationController::class, 'store']);
    Route::get('/{id}', [LocationController::class, 'show']);
    Route::put('/{id}', [LocationController::class, 'update']);
    Route::delete('/{id}', [LocationController::class, 'destroy']);
});

// Attendance
Route::prefix('attendance')->group(function () {
    Route::post('/clock-in', [GuardAttendanceController::class, 'clockIn']);
    Route::post('/clock-out', [GuardAttendanceController::class, 'clockOut']);
    Route::get('/today/{guard_id}', [GuardAttendanceController::class, 'todayAttendance']);
    Route::get('/list', [GuardAttendanceController::class, 'listAttendances']);
});



Route::middleware('auth:sanctum')->get('/profile', [UserController::class, 'profile']);
Route::put('/users/{id}/profile', [UserController::class, 'updateProfile']);
Route::delete('/users/{id}/profile', [UserController::class,'destroyProfile']);
Route::get('/users', [UserController::class,'getAllUser']);


Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::post('/notifications/{id}/respond', [NotificationController::class, 'respond']);
Route::get('/guards/{guard_id}/notifications', [NotificationController::class, 'guardNotifications']);

Route::prefix('role-permissions')->group(function () {
    Route::get('metadata', [RolePermissionController::class, 'metadata']); 
    Route::post('/', [RolePermissionController::class, 'store']);
    Route::get('{role_id}', [RolePermissionController::class, 'show']);
    Route::put('{role_id}', [RolePermissionController::class, 'update']);
});
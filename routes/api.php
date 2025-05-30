<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user/status', [UserController::class, 'status']);
});

Route::middleware('auth:sanctum')->get('/user/status', [UserController::class, 'status']);



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
});





Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class); // ← これだけでOK
});

// パスワードリセットメール送信
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest');

// 新しいパスワードの設定
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest');
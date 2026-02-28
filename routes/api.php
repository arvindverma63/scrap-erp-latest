<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);


Route::get('/homepage', [HomeController::class, 'homepage']);
Route::post('details', [HomeController::class, 'details']);
Route::post('search-product', [HomeController::class, 'SearchProduct']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products/filter', [ProductController::class, 'filter']);
    Route::get('/products/group-values', [ProductController::class, 'groupValues']);
    Route::get('/products', [HomeController::class, 'index']);
    Route::post('/update-profile', [HomeController::class, 'updateProfile']);
    Route::get('/pages', [HomeController::class, 'page']);
    Route::get('/get-profile', [AuthController::class, 'GetProfile']);
    Route::post('/logout', [AuthController::class, 'Logout']);
});

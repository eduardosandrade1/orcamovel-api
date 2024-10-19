<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('jwt-auth')->group(function () {
    Route::get('user', [UserController::class, 'getUser']);
    Route::post('logout', [UserController::class, 'logout']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/authorization', [AuthController::class, 'authorization']);
Route::post('/registration', [UserController::class, 'store']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/files', [FileController::class, 'store']);
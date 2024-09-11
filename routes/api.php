<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/authorization', [AuthController::class, 'authorization']);
Route::post('/registration', [UserController::class, 'store']);

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/files', [FileController::class, 'store'])->can('create', File::class);
    Route::patch('/files/{file}', [FileController::class, 'update'])->can('update', 'file');
});

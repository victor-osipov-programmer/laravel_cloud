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
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->can('delete', 'file');
    Route::get('/files/disk', [FileController::class, 'index']);
    Route::get('/files/{file}', [FileController::class, 'show'])->can('view', 'file');
    Route::post('/files/{file}/accesses', [FileController::class, 'addAccess'])->can('update', 'file');
    Route::delete('/files/{file}/accesses', [FileController::class, 'deleteAccess'])->can('update', 'file');
    Route::get('/shared', [FileController::class, 'accesses']);
});

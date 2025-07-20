<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::post('/login', [AuthController::class, 'login']);

// Logout route (protected by authentication middleware)
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

// Admin Dashboard (accessible only by SuperAdmin)
Route::middleware(['auth:api', 'role:SuperAdmin'])->get('/admin-dashboard', [AdminController::class, 'index']);

// List Users (Role-based access control)
Route::middleware(['auth:api'])->get('/users', [UserController::class, 'list']);

Route::middleware(['auth:api', 'role:5'])->post('/users/bulk-create', [UserController::class, 'bulkCreate']);

Route::middleware('auth:api')->put('/users/{id}', [UserController::class, 'updateUser']);
Route::middleware('auth:api')->delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::middleware('auth:api')->post('/users/{id}/restore', [UserController::class, 'restoreUser']);

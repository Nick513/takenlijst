<?php

use \App\Models\Task;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Default
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('/register', [AuthController::class, 'register'])->name('api_register');
Route::post('/login', [AuthController::class, 'login'])->name('api_login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('api_logout');

// User
Route::post('/user/update/amountoftasks', [UserController::class, 'updateAmountOfTasks']);

// Tasks
Route::get('/tasks/snippet', [TaskController::class, 'snippet']);
Route::get('/tasks/links', [TaskController::class, 'links']);
Route::get('/tasks/{identifier?}', [TaskController::class, 'tasks']);
Route::post('/tasks/add', [TaskController::class, 'addTask']);
Route::post('/tasks/order', [TaskController::class, 'orderTasks']);
Route::post('/tasks/edit/{identifier}', [TaskController::class, 'editTask']);
Route::post('/tasks/toggle/{identifier}', [TaskController::class, 'toggleTask']);
Route::delete('/tasks/delete', [TaskController::class, 'deleteTask']);
Route::delete('/tasks/delete/all', [TaskController::class, 'deleteAllTasks']);

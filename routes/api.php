<?php

use \App\Models\Task;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Auth
Route::middleware('auth:web')->get('/user', function (Request $request) { // auth:sanctum
    return $request->user();
});

// Tasks
Route::get('/tasks', [App\Http\Controllers\Api\TaskController::class, 'tasks']);
Route::post('/tasks/add', [App\Http\Controllers\Api\TaskController::class, 'addTask']);
Route::post('/tasks/edit/{identifier}', [App\Http\Controllers\Api\TaskController::class, 'editTask']);
Route::post('/tasks/toggle/{identifier}', [App\Http\Controllers\Api\TaskController::class, 'toggleTask']);
Route::delete('/tasks/delete', [App\Http\Controllers\Api\TaskController::class, 'deleteTask']);
Route::delete('/tasks/delete/all', [App\Http\Controllers\Api\TaskController::class, 'deleteAllTasks']);
Route::post('/tasks/order', [App\Http\Controllers\Api\TaskController::class, 'orderTasks']);
Route::get('/tasks/snippet', [App\Http\Controllers\Api\TaskController::class, 'snippet']);

// Login
// ...

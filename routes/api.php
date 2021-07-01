<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\authController;
use App\Http\Controllers\TaskController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/auth/signup', [authController::class,'register']);
Route::post('/auth/signin', [authController::class,'login']);
Route::post('/tasks', [TaskController::class,'createTask']);
Route::middleware('auth:api')->group(function () {
    Route::post('/tasks', [TaskController::class,'createTask']);
    Route::get('/tasks', [TaskController::class,'getTasks']);
    Route::get('/tasks/{id}', [TaskController::class,'getTaskById']);
    Route::delete('/tasks/{id}', [TaskController::class,'deleteTaskById']);
    Route::patch('/tasks/{id}/status', [TaskController::class,'updateTaskById']);
});

<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardUserController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

Route::apiResource('task', 'App\Http\Controllers\TaskController')->middleware('auth:sanctum');
Route::apiResource('board', 'App\Http\Controllers\BoardController')->middleware('auth:sanctum');


Route::post('/task/{taskId}/assign-user', [TaskController::class, 'assignUser'])
    ->middleware('auth:sanctum');
Route::post('/task/{taskId}/unassign-user', [TaskController::class, 'unassignUser'])
    ->middleware('auth:sanctum');
Route::get('/tasks', [TaskController::class, 'getTasksByUser'])
    ->middleware('auth:sanctum');
Route::get('/tasks/{slug}', [TaskController::class, 'getTaskBySlug'])
    ->middleware('auth:sanctum');

Route::get('/boards', [BoardController::class, 'getBoardsByUser'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/tasks', [TaskController::class, 'getTasksByBoard'])
    ->middleware('auth:sanctum');
Route::post('/boards/{boardId}/tasks', [TaskController::class, 'createTask'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}', [BoardController::class, 'getBoard'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/statuses', [BoardController::class, 'getStatusesByBoard'])
    ->middleware('auth:sanctum');
Route::put('boards/{boardId}/statuses/order', [BoardController::class, 'updateStatusOrder'])
    ->middleware('auth:sanctum');
Route::post('boards/{boardId}/statuses', [BoardController::class, 'addStatusToBoard'])
    ->middleware('auth:sanctum');
Route::put('boards/{boardId}/status/{statusId}', [BoardController::class, 'updateStatus'])
    ->middleware('auth:sanctum');
Route::delete('boards/{boardId}/status/{statusId}', [BoardController::class, 'destroyStatus'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/users', [BoardController::class, 'getUsersByBoard'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/priorities', [BoardController::class, 'getPrioritiesByBoard'])
    ->middleware('auth:sanctum');
Route::post('/board/{boardId}/assign-user', [BoardUserController::class, 'assignUser'])
    ->middleware('auth:sanctum');
Route::post('/board/{boardId}/unassign-user', [BoardUserController::class, 'unassignUser'])
    ->middleware('auth:sanctum');



Route::get('/users', function () {
    return \App\Models\User::all();
})->middleware('auth:sanctum');





<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardUserController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

Route::apiResource('task', 'App\Http\Controllers\TaskController');
Route::apiResource('board', 'App\Http\Controllers\BoardController');

Route::get('/tasks', [TaskController::class, 'getTasksByUser']);
Route::get('/tasks/{slug}', [TaskController::class, 'getTaskBySlug']);

Route::get('/boards', [BoardController::class, 'getBoardsByUser'])
->middleware('auth:sanctum');
Route::get('/board/{boardId}/tasks', [TaskController::class, 'getTasksByBoard']);
Route::get('/board/{boardId}', [BoardController::class, 'getBoard']);
Route::get('/board/{boardId}/statuses', [BoardController::class, 'getStatusesByBoard']);

// list users
Route::get('/users', function () {
    return \App\Models\User::all();
})->middleware('auth:sanctum');

Route::post('/board/{boardId}/assign-user', [BoardUserController::class, 'assignUser'])
->middleware('auth:sanctum');
Route::post('/board/{boardId}/unassign-user', [BoardUserController::class, 'unassignUser'])
->middleware('auth:sanctum');

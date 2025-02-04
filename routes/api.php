<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardUserController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppAuthController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});




Route::apiResource('task', TaskController::class)->middleware('auth:sanctum');
Route::apiResource('board', BoardController::class)->middleware('auth:sanctum');


Route::post('/task/{taskId}/assign-user', [TaskController::class, 'assignUser'])
    ->middleware('auth:sanctum');
Route::post('/task/{taskId}/unassign-user', [TaskController::class, 'unassignUser'])
    ->middleware('auth:sanctum');
Route::post('/task/{taskId}/delay', [TaskController::class, 'delayTask'])
    ->middleware('auth:sanctum');
Route::get('/tasks', [TaskController::class, 'getTasksByUser'])
    ->middleware('auth:sanctum');
Route::get('/tasks/{slug}', [TaskController::class, 'getTaskBySlug'])
    ->middleware('auth:sanctum');

Route::get('/boards', [BoardController::class, 'getBoardsByUser'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/tasks', [TaskController::class, 'getTasksByBoard'])
    ->middleware('auth:sanctum');
Route::post('/board/{boardId}/tasks', [TaskController::class, 'createTask'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}', [BoardController::class, 'getBoard'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/statuses', [BoardController::class, 'getStatusesByBoard'])
    ->middleware('auth:sanctum');
Route::put('board/{boardId}/statuses/order', [BoardController::class, 'updateStatusOrder'])
    ->middleware('auth:sanctum');
Route::post('board/{boardId}/statuses', [BoardController::class, 'addStatusToBoard'])
    ->middleware('auth:sanctum');
Route::put('board/{boardId}/status/{statusId}', [BoardController::class, 'updateStatus'])
    ->middleware('auth:sanctum');
Route::delete('board/{boardId}/status/{statusId}', [BoardController::class, 'destroyStatus'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/users', [BoardController::class, 'getUsersByBoard'])
    ->middleware('auth:sanctum');
Route::get('/board/{boardId}/priorities', [BoardController::class, 'getPrioritiesByBoard'])
    ->middleware('auth:sanctum');
Route::post('/board/{boardId}/assign-user', [BoardUserController::class, 'assignUser'])
    ->middleware('auth:sanctum');
Route::post('/board/{boardId}/unassign-user', [BoardUserController::class, 'unassignUser'])
    ->middleware('auth:sanctum');

Route::apiResource('meeting', MeetingController::class);
Route::post('/meeting/{meetingId}/late', [MeetingController::class, 'notifyLate'])
    ->middleware('auth:sanctum');
Route::post('/meeting/{meetingId}/absent', [MeetingController::class, 'notifyAbsent'])
    ->middleware('auth:sanctum');

Route::post('board/{boardId}/meeting', [MeetingController::class, 'storeForBoard'])
->middleware('auth:sanctum');
Route::put('board/{boardId}/meeting/{meetingId}', [MeetingController::class, 'updateForBoard'])
    ->middleware('auth:sanctum');
Route::delete('board/{boardId}/meeting/{meetingId}', [MeetingController::class, 'destroyForBoard'])
->middleware('auth:sanctum');
Route::get('board/{boardId}/meetings', [MeetingController::class, 'getMeetingsByBoard'])
->middleware('auth:sanctum');
Route::get('board/{boardId}/meetings/date-range/from/{dateFrom}/to/{dateTo}', [MeetingController::class, 'getMeetingsByDateRange'])
    ->middleware('auth:sanctum');

Route::post('/token', [AppAuthController::class, 'requestToken'])
    ->middleware('guest');

Route::post('/revoke-token', [AppAuthController::class, 'revokeToken'])
    ->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::get('/user/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');
Route::put('/user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');



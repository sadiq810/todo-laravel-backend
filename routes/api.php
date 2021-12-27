<?php

use App\Models\Task;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomerController;
use \App\Http\Controllers\Api\{TaskController, CheckInController};

Route::get('me', fn(\Illuminate\Http\Request $request) => $request->user())->middleware('auth:sanctum');

Route::post('login', [CustomerController::class, 'authenticate']);
Route::post('register', [CustomerController::class, 'register']);
Route::post('reset-password', [CustomerController::class, 'resetPassword']);
Route::post('update-customer-profile', [CustomerController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('update-customer-password', [CustomerController::class, 'updatePassword'])->middleware('auth:sanctum');

Route::post('create-task', [TaskController::class, 'create'])->middleware('auth:sanctum');
Route::get('tasks', [TaskController::class, 'list'])->middleware('auth:sanctum');
Route::post('task-completed', [TaskController::class, 'complete'])->middleware('auth:sanctum');
Route::delete('task', [TaskController::class, 'delete'])->middleware('auth:sanctum');

Route::get('locations', [CheckInController::class, 'index'])->middleware('auth:sanctum');
Route::post('checkin', [CheckInController::class, 'save'])->middleware('auth:sanctum');
Route::delete('checkin', [CheckInController::class, 'delete'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('logout', [CustomerController::class, 'logout']);
Route::middleware('auth:sanctum')->get('user', [CustomerController::class, 'user']);

Route::get('notify', function () {
    \Illuminate\Support\Facades\Artisan::call('check:tasks');
});

Route::get('do-notify', function () {
    $tasks = Task::whereDate('due_date', '<', now())
        ->whereTime('due_date', '<', now())
        ->where('is_complete', 0)
        ->where('is_notify', 0)
        ->limit(200)
        ->get();

    foreach ($tasks as $task) {
        $task->update(['is_notify'=> 1]);
        event(new \App\Events\Notify($task));
    }

    return ['status' => true, 'tasks' =>  $tasks->count()];
});

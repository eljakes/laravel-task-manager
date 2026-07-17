<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes handle task and project management pages.
| Resource routes create the standard CRUD endpoints.
|
*/

Route::post('tasks/reorder', [TaskController::class, 'reorder'])
    ->name('tasks.reorder');

Route::resource('tasks', TaskController::class)
    ->except('show');

Route::resource('projects', ProjectController::class)
    ->except('show');

// Redirect the home page to the main task list.
Route::redirect('/', '/tasks');

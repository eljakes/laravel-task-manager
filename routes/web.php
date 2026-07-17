<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes handle all project-related pages.
| Resource routes automatically create the standard CRUD endpoints.
|
*/

Route::resource('projects', ProjectController::class);

// Redirect the home page to the projects list.
Route::redirect('/', '/projects');

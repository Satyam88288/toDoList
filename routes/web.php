<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/', [TaskController::class, 'index']);
Route::get('/tasks', [TaskController::class, 'getTask'])->name('tasks');
Route::get('/all-tasks', [TaskController::class, 'getAllTask'])->name('all-task');
// Route::get('/all-tasks', [TaskController::class, 'getAllTask'])->name('all-task');
Route::post('/tasks-store', [TaskController::class, 'store'])->name('tasks-store');
Route::post('/tasks-updae', [TaskController::class, 'update'])->name('tasks-update');
Route::delete('/tasks-delete', [TaskController::class, 'destroy'])->name('tasks-delete');

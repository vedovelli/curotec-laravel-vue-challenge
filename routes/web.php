<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CreateTaskController;
use App\Http\Controllers\UpdateTaskController;
use App\Http\Controllers\DeleteTaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Task routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('tasks/create', CreateTaskController::class)->name('tasks.create');
    Route::post('tasks', [CreateTaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}', TaskController::class)->name('tasks.show');
    Route::get('tasks/{task}/edit', UpdateTaskController::class)->name('tasks.edit');
    Route::put('tasks/{task}', [UpdateTaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', DeleteTaskController::class)->name('tasks.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\CreateTaskAction;
use App\Actions\DeleteTaskAction;
use App\Actions\GetTaskStatsAction;
use App\Actions\UpdateTaskAction;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListTasksController;
use App\Models\Task;
use Illuminate\Support\ServiceProvider;

final class TaskActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register CreateTaskAction with Task model dependency
        $this->app->bind(CreateTaskAction::class, function ($app) {
            return new CreateTaskAction($app->make(Task::class));
        });

        // Register UpdateTaskAction with Task model dependency
        $this->app->bind(UpdateTaskAction::class, function ($app) {
            return new UpdateTaskAction($app->make(Task::class));
        });

        // Register DeleteTaskAction with Task model dependency
        $this->app->bind(DeleteTaskAction::class, function ($app) {
            return new DeleteTaskAction($app->make(Task::class));
        });

        // Register GetTaskStatsAction with Task model dependency
        $this->app->bind(GetTaskStatsAction::class, function ($app) {
            return new GetTaskStatsAction($app->make(Task::class));
        });

        // Register DashboardController with dependencies
        $this->app->bind(DashboardController::class, function ($app) {
            return new DashboardController(
                $app->make(Task::class),
                $app->make(GetTaskStatsAction::class)
            );
        });

        // Register ListTasksController with dependencies
        $this->app->bind(ListTasksController::class, function ($app) {
            return new ListTasksController(
                $app->make(GetTaskStatsAction::class)
            );
        });

        // Register Task model as singleton for better performance
        $this->app->singleton(Task::class, function ($app) {
            return new Task();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetTaskStatsAction;
use App\Http\Resources\TaskResource;
use App\Http\Traits\TransformsPagination;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Dashboard Controller
 * 
 * Handles the main dashboard view that displays task overview,
 * statistics, and paginated task list for the Inertia.js application.
 */
class DashboardController extends Controller
{
    use TransformsPagination;

    public function __construct(
        private readonly Task $task,
        private readonly GetTaskStatsAction $getTaskStatsAction
    ) {}

    /**
     * Display the dashboard with task overview and statistics.
     */
    public function __invoke(Request $request): Response
    {
        // Get paginated tasks with latest first
        $tasks = $this->task->latest()->paginate(10);
        
        // Get task statistics
        $stats = $this->getTaskStatsAction->execute();

        return Inertia::render('Dashboard', [
            'tasks' => TaskResource::collection($tasks),
            'stats' => $stats,
            'pagination' => $this->transformPaginationWithExtras($tasks),
        ]);
    }
} 
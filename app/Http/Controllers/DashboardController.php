<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetTaskStatsAction;
use App\Http\Resources\TaskResource;
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

    public function __construct(
        private readonly Task $task,
        private readonly GetTaskStatsAction $getTaskStatsAction
    ) {}

    /**
     * Display the dashboard with task overview and statistics.
     */
    public function __invoke(Request $request): Response
    {
        $tasks = $this->task->pending()->get()->filter(
            fn ($task) => $task->days_until_due < 1
        )->take(5);

        $stats = $this->getTaskStatsAction->execute();

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }
}

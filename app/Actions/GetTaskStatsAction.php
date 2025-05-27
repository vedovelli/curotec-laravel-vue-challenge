<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

/**
 * @extends BaseAction<null, array<string, int|float>>
 */
class GetTaskStatsAction extends BaseAction
{
    public function __construct(
        private Task $task
    ) {}

    /**
     * Get task statistics.
     *
     * @param  null  $input Not used for this action
     * @return array<string, int|float>
     */
    public function handle(mixed $input = null): array
    {
        $totalTasks = $this->task->count();
        $completedTasks = $this->task->completed()->count();
        $pendingTasks = $this->task->pending()->count();
        
        $completionPercentage = $totalTasks > 0 
            ? (float) round(($completedTasks / $totalTasks) * 100, 2) 
            : 0.0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'completion_percentage' => $completionPercentage,
        ];
    }
} 
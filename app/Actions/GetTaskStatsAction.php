<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

final readonly class GetTaskStatsAction
{
    public function __construct(
        private Task $task
    ) {}

    public function handle(): array
    {
        $totalTasks = $this->task->count();
        $completedTasks = $this->task->where('status', Task::STATUS_COMPLETED)->count();
        $pendingTasks = $this->task->where('status', Task::STATUS_PENDING)->count();
        
        $completionPercentage = $totalTasks > 0 
            ? round(($completedTasks / $totalTasks) * 100, 2) 
            : 0.0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'completion_percentage' => $completionPercentage,
        ];
    }
} 
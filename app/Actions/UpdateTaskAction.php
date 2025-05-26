<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpdateTaskAction
{
    public function __construct(
        private Task $taskModel
    ) {}

    /**
     * Update an existing task with the provided data.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ModelNotFoundException
     */
    public function handle(int $taskId, array $data): Task
    {
        $task = $this->taskModel->findOrFail($taskId);

        $task->update($data);

        return $task->fresh();
    }
}

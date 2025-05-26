<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

final readonly class CreateTaskAction
{
    public function __construct(
        private Task $taskModel
    ) {}

    /**
     * Create a new task with the provided data.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): Task
    {
        return $this->taskModel->create($data);
    }
}

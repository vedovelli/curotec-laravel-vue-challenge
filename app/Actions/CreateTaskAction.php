<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

/**
 * @extends BaseAction<array<string, mixed>, Task>
 */
class CreateTaskAction extends BaseAction
{
    public function __construct(
        private Task $taskModel
    ) {}

    /**
     * Create a new task with the provided data.
     *
     * @param  array<string, mixed>  $input
     */
    public function handle(mixed $input = null): Task
    {
        $this->validateInputNotNull($input, 'Task data cannot be null');
        $this->validateInputType($input, 'array', 'Task data must be an array');

        return $this->taskModel->create($input);
    }
}

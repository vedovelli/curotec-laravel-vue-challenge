<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

/**
 * Create Task Action
 * 
 * Handles the creation of new tasks. This action assumes that the input data
 * has already been validated by the controller using CreateTaskRequest.
 * It focuses solely on the business logic of task creation.
 * 
 * @template TInput array
 * @template TOutput Task
 */
final class CreateTaskAction extends BaseAction
{
    /**
     * Handle the task creation.
     * 
     * @param array $input The validated task data to create
     * @return Task The created task instance
     */
    public function handle(mixed $input = null): Task
    {
        // Create the task with the validated data
        $task = Task::create($this->prepareTaskData($input));

        return $task;
    }

    /**
     * Prepare the task data for creation.
     * 
     * @param array $input The validated input data
     * @return array<string, mixed> The prepared data for task creation
     */
    private function prepareTaskData(array $input): array
    {
        $data = [
            'title' => trim($input['title']),
            'status' => $input['status'],
        ];

        // Add optional fields if present
        if (isset($input['description']) && !empty(trim($input['description']))) {
            $data['description'] = trim($input['description']);
        }

        if (isset($input['due_date']) && !empty($input['due_date'])) {
            $data['due_date'] = $input['due_date'];
        }

        return $data;
    }
} 
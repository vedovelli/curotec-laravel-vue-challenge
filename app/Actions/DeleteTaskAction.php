<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class DeleteTaskAction extends BaseAction
{
    public function __construct(
        private Task $task
    ) {}

    /**
     * Delete a task by ID.
     *
     * @param  mixed  $input Task ID to delete
     */
    public function handle(mixed $input = null): bool
    {
        $this->validateInputNotNull($input, 'Task ID cannot be null');
        $this->validateInputType($input, 'integer', 'Task ID must be an integer');

        try {
            $task = $this->task->findOrFail($input);
            return $task->delete();
        } catch (ModelNotFoundException $e) {
            Log::warning('Attempted to delete non-existent task', ['id' => $input]);
            throw $e;
        }
    }
} 
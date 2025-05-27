<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateTaskAction extends BaseAction
{
    public function __construct(
        private Task $taskModel
    ) {}

    /**
     * Update an existing task with the provided data.
     *
     * @param  mixed  $input Array containing 'id' and 'data' keys
     *
     * @throws ModelNotFoundException
     */
    public function handle(mixed $input = null): Task
    {
        $this->validateInputNotNull($input, 'Update data cannot be null');
        $this->validateInputType($input, 'array', 'Update data must be an array');
        $this->validateArrayInput($input, ['id', 'data'], 'Update data must contain id and data keys');

        $taskId = $input['id'];
        $data = $input['data'];

        $task = $this->taskModel->findOrFail($taskId);
        $task->update($data);

        return $task->fresh();
    }
}

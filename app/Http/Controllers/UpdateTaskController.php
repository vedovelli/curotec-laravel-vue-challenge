<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateTaskAction;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UpdateTaskController extends Controller
{
    public function __construct(
        private readonly UpdateTaskAction $updateTaskAction
    ) {}

    /**
     * Show the task edit form.
     */
    public function __invoke(Task $task): Response
    {
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->updateTaskAction->handle($task->id, $request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }
} 
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteTaskAction;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class DeleteTaskController extends Controller
{
    public function __construct(
        private readonly DeleteTaskAction $deleteTaskAction
    ) {}

    /**
     * Delete the specified task.
     */
    public function __invoke(Task $task): RedirectResponse
    {
        $this->deleteTaskAction->handle($task->id);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task deleted successfully.');
    }
} 
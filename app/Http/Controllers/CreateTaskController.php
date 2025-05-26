<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateTaskAction;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CreateTaskController extends Controller
{
    public function __construct(
        private readonly CreateTaskAction $createTaskAction
    ) {}

    /**
     * Show the task creation form.
     */
    public function __invoke(): Response
    {
        return Inertia::render('Tasks/Create');
    }

    /**
     * Store a newly created task.
     */
    public function store(CreateTaskRequest $request): RedirectResponse
    {
        $task = $this->createTaskAction->handle($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully!');
    }
} 
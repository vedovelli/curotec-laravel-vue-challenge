<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display the specified task.
     */
    public function __invoke(Task $task): Response
    {
        return Inertia::render('Tasks/Show', [
            'task' => (new TaskResource($task))->resolve(),
        ]);
    }
} 
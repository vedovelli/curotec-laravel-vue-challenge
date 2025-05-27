<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListTasksController extends Controller
{

    public function __invoke(Request $request): Response
    {
        // Validate query parameters
        $validated = $request->validate([
            'status' => 'nullable|string|in:all,pending,completed',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $filter = $validated['status'] ?? 'all';
        $perPage = $validated['per_page'] ?? 12;

        // Build query with filtering
        $query = Task::query()->orderBy('created_at', 'desc');

        // Apply status filter using scopes
        if ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'completed') {
            $query->completed();
        }
        // 'all' filter requires no additional scope

        // Get paginated results
        $tasks = $query->paginate($perPage);

        // Append query parameters to pagination links
        $tasks->appends($request->query());

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'currentFilter' => $filter,
        ]);
    }
} 
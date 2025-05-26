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

        // Apply status filter
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Get paginated results
        $tasks = $query->paginate($perPage);

        // Append query parameters to pagination links
        $tasks->appends($request->query());

        // Transform Laravel pagination to match frontend interface
        $transformedTasks = [
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
            ],
            'links' => $tasks->linkCollection()->toArray(),
        ];

        return Inertia::render('Tasks/Index', [
            'tasks' => $transformedTasks,
            'currentFilter' => $filter,
        ]);
    }
} 
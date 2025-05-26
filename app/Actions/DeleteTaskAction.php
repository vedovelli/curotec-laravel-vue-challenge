<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

final readonly class DeleteTaskAction
{
    public function __construct(
        private Task $task
    ) {}

    public function handle(int $id): bool
    {
        try {
            $task = $this->task->findOrFail($id);
            return $task->delete();
        } catch (ModelNotFoundException $e) {
            Log::warning('Attempted to delete non-existent task', ['id' => $id]);
            throw $e;
        }
    }
} 
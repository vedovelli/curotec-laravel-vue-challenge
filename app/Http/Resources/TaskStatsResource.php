<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Task Stats Resource
 * 
 * Transforms task statistics data into a consistent JSON structure
 * for dashboard consumption in the Inertia.js application.
 */
final class TaskStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_tasks' => $this->resource['total_tasks'],
            'completed_tasks' => $this->resource['completed_tasks'],
            'pending_tasks' => $this->resource['pending_tasks'],
            'completion_percentage' => $this->resource['completion_percentage'],
        ];
    }
} 
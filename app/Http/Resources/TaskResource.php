<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Task;

/**
 * Task Resource
 * 
 * Transforms Task model instances into a consistent JSON structure
 * for frontend consumption in the Inertia.js application.
 * 
 * @mixin Task
 */
final class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'due_date' => $this->due_date?->toISOString(),
            'formatted_due_date' => $this->formatted_due_date,
            'is_completed' => $this->is_completed,
            'is_overdue' => $this->is_overdue,
            'days_until_due' => $this->days_until_due,
            'priority_level' => $this->priority_level,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
} 
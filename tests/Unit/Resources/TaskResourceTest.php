<?php

declare(strict_types=1);

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'status' => Task::STATUS_PENDING,
        'due_date' => now()->addDays(3),
    ]);
});

it('transforms task model to array with all required fields', function (): void {
    // Arrange
    $resource = new TaskResource($this->task);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result)->toBeArray()
        ->and($result)->toHaveKeys([
            'id',
            'title',
            'description',
            'status',
            'status_text',
            'due_date',
            'formatted_due_date',
            'is_completed',
            'is_overdue',
            'days_until_due',
            'priority_level',
            'created_at',
            'updated_at',
        ])
        ->and($result['id'])->toBe($this->task->id)
        ->and($result['title'])->toBe('Test Task')
        ->and($result['description'])->toBe('Test Description')
        ->and($result['status'])->toBe(Task::STATUS_PENDING)
        ->and($result['status_text'])->toBe('Pending')
        ->and($result['is_completed'])->toBeFalse()
        ->and($result['is_overdue'])->toBeFalse();
});

it('handles null due_date correctly', function (): void {
    // Arrange
    $taskWithoutDueDate = Task::factory()->create([
        'due_date' => null,
    ]);
    $resource = new TaskResource($taskWithoutDueDate);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['due_date'])->toBeNull()
        ->and($result['formatted_due_date'])->toBeNull()
        ->and($result['days_until_due'])->toBeNull();
});

it('formats dates as ISO strings', function (): void {
    // Arrange
    $resource = new TaskResource($this->task);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['due_date'])->toBeString()
        ->and($result['created_at'])->toBeString()
        ->and($result['updated_at'])->toBeString()
        ->and($result['due_date'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/')
        ->and($result['created_at'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/');
});

it('correctly identifies completed tasks', function (): void {
    // Arrange
    $completedTask = Task::factory()->create([
        'status' => Task::STATUS_COMPLETED,
    ]);
    $resource = new TaskResource($completedTask);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['status'])->toBe(Task::STATUS_COMPLETED)
        ->and($result['status_text'])->toBe('Completed')
        ->and($result['is_completed'])->toBeTrue();
});

it('correctly identifies overdue tasks', function (): void {
    // Arrange
    $overdueTask = Task::factory()->create([
        'status' => Task::STATUS_PENDING,
        'due_date' => now()->subDays(1),
    ]);
    $resource = new TaskResource($overdueTask);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['is_overdue'])->toBeTrue()
        ->and($result['is_completed'])->toBeFalse()
        ->and($result['status'])->toBe(Task::STATUS_PENDING);
}); 
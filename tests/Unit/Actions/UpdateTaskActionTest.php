<?php

declare(strict_types=1);

use App\Actions\UpdateTaskAction;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function (): void {
    $this->action = new UpdateTaskAction(new Task);
});

it('updates existing task successfully', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'status' => 'pending',
    ]);

    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => 'completed',
    ];

    // Act
    $result = $this->action->handle($task->id, $updateData);

    // Assert
    expect($result)->toBeInstanceOf(Task::class)
        ->and($result->title)->toBe('Updated Title')
        ->and($result->description)->toBe('Updated Description')
        ->and($result->status)->toBe('completed')
        ->and($result->id)->toBe($task->id);

    // Verify the task was actually updated in the database
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => 'completed',
    ]);
});

it('updates partial task data', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'status' => 'pending',
    ]);

    $updateData = [
        'title' => 'Updated Title Only',
    ];

    // Act
    $result = $this->action->handle($task->id, $updateData);

    // Assert
    expect($result->title)->toBe('Updated Title Only')
        ->and($result->description)->toBe('Original Description')
        ->and($result->status)->toBe('pending');
});

it('throws exception for non-existent task', function (): void {
    // Arrange
    $nonExistentTaskId = 999;
    $updateData = [
        'title' => 'Updated Title',
    ];

    // Act & Assert
    expect(fn() => $this->action->handle($nonExistentTaskId, $updateData))
        ->toThrow(ModelNotFoundException::class);
});

it('returns fresh model instance', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Original Title',
    ]);

    $updateData = [
        'title' => 'Updated Title',
    ];

    // Act
    $result = $this->action->handle($task->id, $updateData);

    // Assert
    expect($result)->not->toBe($task)
        ->and($result->id)->toBe($task->id)
        ->and($result->title)->toBe('Updated Title');
});

it('handles empty update data', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
    ]);

    $updateData = [];

    // Act
    $result = $this->action->handle($task->id, $updateData);

    // Assert
    expect($result->title)->toBe('Original Title')
        ->and($result->description)->toBe('Original Description')
        ->and($result->id)->toBe($task->id);
});

it('is final and readonly class', function (): void {
    // Assert
    $reflection = new ReflectionClass(UpdateTaskAction::class);
    
    expect($reflection->isFinal())->toBeTrue()
        ->and($reflection->isReadOnly())->toBeTrue();
});

it('accepts task model in constructor', function (): void {
    // Arrange & Act
    $taskModel = new Task;
    $action = new UpdateTaskAction($taskModel);

    // Assert
    expect($action)->toBeInstanceOf(UpdateTaskAction::class);
});

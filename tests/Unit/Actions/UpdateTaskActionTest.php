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
    $result = $this->action->handle([
        'id' => $task->id,
        'data' => $updateData
    ]);

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
    $result = $this->action->handle([
        'id' => $task->id,
        'data' => $updateData
    ]);

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
    expect(fn() => $this->action->handle([
        'id' => $nonExistentTaskId,
        'data' => $updateData
    ]))->toThrow(ModelNotFoundException::class);
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
    $result = $this->action->handle([
        'id' => $task->id,
        'data' => $updateData
    ]);

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
    $result = $this->action->handle([
        'id' => $task->id,
        'data' => $updateData
    ]);

    // Assert
    expect($result->title)->toBe('Original Title')
        ->and($result->description)->toBe('Original Description')
        ->and($result->id)->toBe($task->id);
});

it('extends BaseAction class', function (): void {
    // Assert
    $reflection = new ReflectionClass(UpdateTaskAction::class);
    
    expect($reflection->getParentClass()->getName())->toBe('App\Actions\BaseAction');
});

it('validates input correctly', function (): void {
    // Test null input validation
    expect(fn() => $this->action->handle(null))
        ->toThrow(\InvalidArgumentException::class, 'Update data cannot be null');
    
    // Test non-array input validation
    expect(fn() => $this->action->handle('invalid'))
        ->toThrow(\InvalidArgumentException::class, 'Update data must be an array');
    
    // Test missing required keys
    expect(fn() => $this->action->handle(['id' => 1]))
        ->toThrow(\InvalidArgumentException::class, 'Update data must contain id and data keys');
    
    expect(fn() => $this->action->handle(['data' => []]))
        ->toThrow(\InvalidArgumentException::class, 'Update data must contain id and data keys');
});

it('works with execute method for logging', function (): void {
    // Arrange
    $task = Task::factory()->create(['title' => 'Original Title']);
    $updateData = ['title' => 'Execute Method Updated Title'];

    // Act
    $result = $this->action->execute([
        'id' => $task->id,
        'data' => $updateData
    ]);

    // Assert
    expect($result)->toBeInstanceOf(Task::class)
        ->and($result->title)->toBe('Execute Method Updated Title')
        ->and($result->id)->toBe($task->id);
});

it('accepts task model in constructor', function (): void {
    // Arrange & Act
    $taskModel = new Task;
    $action = new UpdateTaskAction($taskModel);

    // Assert
    expect($action)->toBeInstanceOf(UpdateTaskAction::class);
});

it('can be resolved from service container with dependency injection', function (): void {
    // Arrange
    $task = Task::factory()->create(['title' => 'Original Title']);
    
    // Act
    $action = app(UpdateTaskAction::class);

    // Assert
    expect($action)->toBeInstanceOf(UpdateTaskAction::class);
    
    // Verify it works with dependency injection
    $updateData = ['title' => 'Container Updated Title'];
    $updatedTask = $action->handle([
        'id' => $task->id,
        'data' => $updateData
    ]);
    
    expect($updatedTask)->toBeInstanceOf(Task::class)
        ->and($updatedTask->title)->toBe('Container Updated Title')
        ->and($updatedTask->id)->toBe($task->id);
});

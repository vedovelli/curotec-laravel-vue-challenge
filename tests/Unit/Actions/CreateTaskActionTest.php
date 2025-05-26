<?php

declare(strict_types=1);

use App\Actions\CreateTaskAction;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateTaskAction();
});

it('can create a task with valid data', function () {
    // Arrange
    $taskData = [
        'title' => 'Test Task',
        'description' => 'This is a test task description',
        'status' => 'pending',
        'due_date' => now()->addDays(7)->format('Y-m-d'),
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class);
    expect($task->exists)->toBeTrue();
    expect($task->title)->toBe('Test Task');
    expect($task->description)->toBe('This is a test task description');
    expect($task->status)->toBe('pending');
    expect($task->due_date)->not->toBeNull();
    
    // Verify task was saved to database
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test task description',
        'status' => 'pending',
    ]);
});

it('can create a task with minimal required data', function () {
    // Arrange
    $taskData = [
        'title' => 'Minimal Task',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class);
    expect($task->exists)->toBeTrue();
    expect($task->title)->toBe('Minimal Task');
    expect($task->description)->toBeNull();
    expect($task->status)->toBe('pending');
    expect($task->due_date)->toBeNull();
});

it('can create a completed task', function () {
    // Arrange
    $taskData = [
        'title' => 'Completed Task',
        'status' => 'completed',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task->status)->toBe('completed');
    expect($task->isCompleted())->toBeTrue();
});

it('trims whitespace from title and description', function () {
    // Arrange
    $taskData = [
        'title' => '  Test Task with Spaces  ',
        'description' => '  Description with spaces  ',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task->title)->toBe('Test Task with Spaces');
    expect($task->description)->toBe('Description with spaces');
});

it('ignores empty description', function () {
    // Arrange
    $taskData = [
        'title' => 'Task without description',
        'description' => '   ',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task->description)->toBeNull();
});

it('accepts due date as today', function () {
    // Arrange
    $taskData = [
        'title' => 'Task due today',
        'status' => 'pending',
        'due_date' => now()->format('Y-m-d'),
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class);
    expect($task->due_date)->not->toBeNull();
    expect($task->due_date->isToday())->toBeTrue();
});

it('can execute action with error handling', function () {
    // Arrange
    $taskData = [
        'title' => 'Test Task',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->execute($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class);
    expect($task->exists)->toBeTrue();
});

it('can execute action with logging', function () {
    // Arrange
    $taskData = [
        'title' => 'Test Task for Execute',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->execute($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class);
    expect($task->title)->toBe('Test Task for Execute');
}); 
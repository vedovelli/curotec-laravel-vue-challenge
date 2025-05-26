<?php

declare(strict_types=1);

use App\Actions\CreateTaskAction;
use App\Models\Task;

beforeEach(function (): void {
    $this->action = new CreateTaskAction(new Task);
});

it('creates task successfully', function (): void {
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
    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->exists)->toBeTrue()
        ->and($task->title)->toBe('Test Task')
        ->and($task->description)->toBe('This is a test task description')
        ->and($task->status)->toBe('pending')
        ->and($task->due_date)->not->toBeNull();

    // Verify task was saved to database
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test task description',
        'status' => 'pending',
    ]);
});

it('creates task with minimal data', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Minimal Task',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->exists)->toBeTrue()
        ->and($task->title)->toBe('Minimal Task')
        ->and($task->description)->toBeNull()
        ->and($task->status)->toBe('pending')
        ->and($task->due_date)->toBeNull();
});

it('creates completed task', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Completed Task',
        'status' => 'completed',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task->status)->toBe('completed')
        ->and($task->isCompleted())->toBeTrue();
});

it('accepts data as provided', function (): void {
    // Arrange - Data should come pre-processed from Form Request
    $taskData = [
        'title' => 'Pre-processed Task',
        'description' => 'Pre-processed Description',
        'status' => 'pending',
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task->title)->toBe('Pre-processed Task')
        ->and($task->description)->toBe('Pre-processed Description');
});

it('handles task with due date', function (): void {
    // Arrange
    $dueDate = now()->addDays(7)->format('Y-m-d');
    $taskData = [
        'title' => 'Task with due date',
        'status' => 'pending',
        'due_date' => $dueDate,
    ];

    // Act
    $task = $this->action->handle($taskData);

    // Assert
    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->due_date)->not->toBeNull()
        ->and($task->due_date->format('Y-m-d'))->toBe($dueDate);
});

it('is final and readonly class', function (): void {
    // Assert
    $reflection = new ReflectionClass(CreateTaskAction::class);
    
    expect($reflection->isFinal())->toBeTrue()
        ->and($reflection->isReadOnly())->toBeTrue();
});

it('accepts task model in constructor', function (): void {
    // Arrange & Act
    $taskModel = new Task;
    $action = new CreateTaskAction($taskModel);

    // Assert
    expect($action)->toBeInstanceOf(CreateTaskAction::class);
});

<?php

declare(strict_types=1);

use App\Actions\DeleteTaskAction;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = new DeleteTaskAction(new Task);
});

it('deletes task successfully', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Task to Delete',
        'description' => 'This task will be deleted',
        'status' => 'pending',
    ]);

    // Act
    $result = $this->action->handle($task->id);

    // Assert
    expect($result)->toBeTrue();
    
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('throws exception for non-existent task', function (): void {
    // Arrange
    $nonExistentId = 999;

    // Act & Assert
    expect(fn() => $this->action->handle($nonExistentId))
        ->toThrow(ModelNotFoundException::class);
});

it('extends BaseAction class', function (): void {
    // Assert
    $reflection = new ReflectionClass(DeleteTaskAction::class);
    
    expect($reflection->getParentClass()->getName())->toBe('App\Actions\BaseAction');
});

it('validates input correctly', function (): void {
    // Test null input validation
    expect(fn() => $this->action->handle(null))
        ->toThrow(\InvalidArgumentException::class, 'Task ID cannot be null');
});

it('works with execute method for logging', function (): void {
    // Arrange
    $task = Task::factory()->create(['title' => 'Execute Method Task']);

    // Act
    $result = $this->action->execute($task->id);

    // Assert
    expect($result)->toBeTrue();
    
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('accepts task model in constructor', function (): void {
    // Arrange & Act
    $taskModel = new Task;
    $action = new DeleteTaskAction($taskModel);

    // Assert
    expect($action)->toBeInstanceOf(DeleteTaskAction::class);
});

it('can be resolved from service container with dependency injection', function (): void {
    // Arrange
    $task = Task::factory()->create();
    
    // Act
    $action = app(DeleteTaskAction::class);

    // Assert
    expect($action)->toBeInstanceOf(DeleteTaskAction::class);
    
    // Verify it works with dependency injection
    $result = $action->handle($task->id);
    
    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

 
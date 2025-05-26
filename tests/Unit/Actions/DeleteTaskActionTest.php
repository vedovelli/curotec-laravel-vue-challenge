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

 
<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->task = Task::factory()->create();
});

it('deletes a task successfully', function (): void {
    // Arrange
    $taskId = $this->task->id;

    // Act
    $response = $this->actingAs($this->user)
        ->delete(route('tasks.destroy', $this->task));

    // Assert
    expect(Task::find($taskId))->toBeNull();

    $response->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Task deleted successfully.');
});

it('requires authentication to delete task', function (): void {
    // Arrange
    $taskId = $this->task->id;

    // Act
    $response = $this->delete(route('tasks.destroy', $this->task));

    // Assert
    $response->assertRedirect(route('login'));
    
    expect(Task::find($taskId))->not->toBeNull();
});

it('returns 404 for non-existent task', function (): void {
    // Act & Assert
    $this->actingAs($this->user)
        ->delete(route('tasks.destroy', 999))
        ->assertNotFound();
});

it('handles deletion of already deleted task gracefully', function (): void {
    // Arrange
    $taskId = $this->task->id;
    $this->task->delete(); // Delete the task first

    // Act & Assert
    $this->actingAs($this->user)
        ->delete(route('tasks.destroy', $taskId))
        ->assertNotFound();
});

it('removes task from database completely', function (): void {
    // Arrange
    $taskId = $this->task->id;
    $taskTitle = $this->task->title;

    // Verify task exists before deletion
    expect(Task::find($taskId))->not->toBeNull();
    expect(Task::where('title', $taskTitle)->exists())->toBeTrue();

    // Act
    $this->actingAs($this->user)
        ->delete(route('tasks.destroy', $this->task));

    // Assert
    expect(Task::find($taskId))->toBeNull();
    expect(Task::where('title', $taskTitle)->exists())->toBeFalse();
    // Verify task is completely removed (hard delete, not soft delete)
});

it('uses BaseAction execute method with logging in debug mode', function (): void {
    // Arrange
    config(['app.debug' => true]);
    Log::spy();
    $taskId = $this->task->id;

    // Act
    $response = $this->actingAs($this->user)
        ->delete(route('tasks.destroy', $this->task));

    // Assert
    expect(Task::find($taskId))->toBeNull();
    
    $response->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Task deleted successfully.');
    
    // Verify BaseAction logging was called
    Log::shouldHaveReceived('debug')
        ->with('Action started: DeleteTaskAction', \Mockery::type('array'))
        ->once();
    
    Log::shouldHaveReceived('debug')
        ->with('Action completed successfully: DeleteTaskAction', \Mockery::type('array'))
        ->once();
}); 
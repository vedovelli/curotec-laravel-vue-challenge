<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('displays a task successfully', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'status' => 'pending',
    ]);

    // Act
    $response = $this->actingAs($this->user)
        ->get(route('tasks.show', $task));

    // Assert
    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tasks/Show')
            ->has('task')
            ->where('task.id', $task->id)
            ->where('task.title', 'Test Task')
            ->where('task.description', 'Test Description')
            ->where('task.status', 'pending')
        );
});

it('returns 404 for non-existent task', function (): void {
    // Act
    $response = $this->actingAs($this->user)
        ->get(route('tasks.show', 999));

    // Assert
    $response->assertNotFound();
});

it('requires authentication to view task', function (): void {
    // Arrange
    $task = Task::factory()->create();

    // Act
    $response = $this->get(route('tasks.show', $task));

    // Assert
    $response->assertRedirect(route('login'));
});

it('displays task with all resource fields', function (): void {
    // Arrange
    $task = Task::factory()->create([
        'title' => 'Complete Task',
        'description' => 'Task with description',
        'status' => 'completed',
        'due_date' => now()->addDays(5),
    ]);

    // Act
    $response = $this->actingAs($this->user)
        ->get(route('tasks.show', $task));

    // Assert
    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tasks/Show')
            ->has('task')
            ->where('task.id', $task->id)
            ->where('task.title', 'Complete Task')
            ->where('task.description', 'Task with description')
            ->where('task.status', 'completed')
            ->where('task.status_text', 'Completed')
            ->where('task.is_completed', true)
            ->has('task.due_date')
            ->has('task.formatted_due_date')
            ->has('task.created_at')
            ->has('task.updated_at')
        );
}); 
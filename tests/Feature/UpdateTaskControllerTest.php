<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->task = Task::factory()->create();
});

it('displays task edit form', function (): void {
    // Act
    $response = $this->actingAs($this->user)
        ->get(route('tasks.edit', $this->task));

    // Assert
    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tasks/Edit')
            ->has('task')
            ->where('task.id', $this->task->id)
            ->where('task.title', $this->task->title)
            ->where('task.description', $this->task->description)
            ->where('task.status', $this->task->status)
            ->where('task.due_date', $this->task->due_date?->toISOString())
        );
});

it('requires authentication to view edit form', function (): void {
    // Act
    $response = $this->get(route('tasks.edit', $this->task));

    // Assert
    $response->assertRedirect(route('login'));
});

it('updates a task successfully with valid data', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Updated Task Title',
        'description' => 'Updated task description',
        'status' => 'completed',
        'due_date' => now()->addDays(10)->format('Y-m-d'),
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $this->task->refresh();
    
    expect($this->task->title)->toBe('Updated Task Title')
        ->and($this->task->description)->toBe('Updated task description')
        ->and($this->task->status)->toBe('completed')
        ->and($this->task->due_date->format('Y-m-d'))->toBe(now()->addDays(10)->format('Y-m-d'));

    $response->assertRedirect(route('tasks.show', $this->task))
        ->assertSessionHas('success', 'Task updated successfully.');
});

it('updates task with partial data', function (): void {
    // Arrange
    $originalTitle = $this->task->title;
    $originalDescription = $this->task->description;
    $updateData = [
        'status' => 'completed',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $this->task->refresh();
    
    expect($this->task->title)->toBe($originalTitle)
        ->and($this->task->description)->toBe($originalDescription)
        ->and($this->task->status)->toBe('completed');

    $response->assertRedirect(route('tasks.show', $this->task));
});

it('clears optional fields when empty values provided', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Updated Title',
        'description' => '',
        'status' => 'pending',
        'due_date' => '',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $this->task->refresh();
    
    expect($this->task->title)->toBe('Updated Title')
        ->and($this->task->description)->toBeNull()
        ->and($this->task->status)->toBe('pending')
        ->and($this->task->due_date)->toBeNull();

    $response->assertRedirect(route('tasks.show', $this->task));
});

it('validates title length when updating', function (): void {
    // Arrange
    $updateData = [
        'title' => str_repeat('a', 256), // Exceeds 255 character limit
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $response->assertSessionHasErrors(['title']);
});

it('validates description length when updating', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Valid Title',
        'description' => str_repeat('a', 1001), // Exceeds 1000 character limit
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $response->assertSessionHasErrors(['description']);
});

it('validates status values when updating', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Valid Title',
        'status' => 'invalid_status',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $response->assertSessionHasErrors(['status']);
});

it('validates due date format when updating', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Valid Title',
        'status' => 'pending',
        'due_date' => 'invalid-date',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $response->assertSessionHasErrors(['due_date']);
});

it('accepts valid due date when updating', function (): void {
    // Arrange
    $futureDate = now()->addDays(5)->format('Y-m-d');
    $updateData = [
        'title' => 'Valid Title',
        'status' => 'pending',
        'due_date' => $futureDate,
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $this->task->refresh();
    expect($this->task->due_date->format('Y-m-d'))->toBe($futureDate);
    
    $response->assertRedirect(route('tasks.show', $this->task));
});

it('requires authentication to update task', function (): void {
    // Arrange
    $originalTitle = $this->task->title;
    $updateData = [
        'title' => 'Updated Title',
        'status' => 'completed',
    ];

    // Act
    $response = $this->put(route('tasks.update', $this->task), $updateData);

    // Assert
    $response->assertRedirect(route('login'));
    
    $this->task->refresh();
    expect($this->task->title)->toBe($originalTitle);
});

it('returns 404 for non-existent task', function (): void {
    // Arrange
    $updateData = [
        'title' => 'Updated Title',
        'status' => 'pending',
    ];

    // Act & Assert
    $this->actingAs($this->user)
        ->put(route('tasks.update', 999), $updateData)
        ->assertNotFound();
}); 
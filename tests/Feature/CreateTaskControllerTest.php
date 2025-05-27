<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('displays task creation form', function (): void {
    // Act
    $response = $this->actingAs($this->user)
        ->get(route('tasks.create'));

    // Assert
    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Tasks/Create')
        );
});

it('requires authentication to view creation form', function (): void {
    // Act
    $response = $this->get(route('tasks.create'));

    // Assert
    $response->assertRedirect(route('login'));
});

it('creates a task successfully with valid data', function (): void {
    // Arrange
    $taskData = [
        'title' => 'New Test Task',
        'description' => 'Test task description',
        'status' => 'pending',
        'due_date' => now()->addDays(7)->format('Y-m-d'),
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $task = Task::where('title', 'New Test Task')->first();
    
    expect($task)->not->toBeNull()
        ->and($task->title)->toBe('New Test Task')
        ->and($task->description)->toBe('Test task description')
        ->and($task->status)->toBe('pending');

    $response->assertRedirect(route('tasks.show', $task))
        ->assertSessionHas('success', 'Task created successfully!');
});

it('creates a task without optional fields', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Minimal Task',
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $task = Task::where('title', 'Minimal Task')->first();
    
    expect($task)->not->toBeNull()
        ->and($task->title)->toBe('Minimal Task')
        ->and($task->description)->toBeNull()
        ->and($task->status)->toBe('pending')
        ->and($task->due_date)->toBeNull();

    $response->assertRedirect(route('tasks.show', $task));
});

it('validates required fields', function (): void {
    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), []);

    // Assert
    $response->assertSessionHasErrors(['title', 'status']);
});

it('validates title length', function (): void {
    // Arrange
    $taskData = [
        'title' => str_repeat('a', 256), // Exceeds 255 character limit
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $response->assertSessionHasErrors(['title']);
});

it('validates description length', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Valid Title',
        'description' => str_repeat('a', 1001), // Exceeds 1000 character limit
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $response->assertSessionHasErrors(['description']);
});

it('validates status values', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Valid Title',
        'status' => 'invalid_status',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $response->assertSessionHasErrors(['status']);
});

it('validates due date is not in the past', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Valid Title',
        'status' => 'pending',
        'due_date' => now()->subDay()->format('Y-m-d'),
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $response->assertSessionHasErrors(['due_date']);
});

it('accepts due date today or in the future', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Valid Title',
        'status' => 'pending',
        'due_date' => now()->format('Y-m-d'), // Today
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $task = Task::where('title', 'Valid Title')->first();
    expect($task)->not->toBeNull();
    
    $response->assertRedirect(route('tasks.show', $task));
});

it('requires authentication to create task', function (): void {
    // Arrange
    $taskData = [
        'title' => 'Test Task',
        'status' => 'pending',
    ];

    // Act
    $response = $this->post(route('tasks.store'), $taskData);

    // Assert
    $response->assertRedirect(route('login'));
    
    expect(Task::where('title', 'Test Task')->exists())->toBeFalse();
});

it('uses BaseAction execute method with logging in debug mode', function (): void {
    // Arrange
    config(['app.debug' => true]);
    Log::spy();
    
    $taskData = [
        'title' => 'BaseAction Test Task',
        'status' => 'pending',
    ];

    // Act
    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    // Assert
    $task = Task::where('title', 'BaseAction Test Task')->first();
    expect($task)->not->toBeNull();
    
    $response->assertRedirect(route('tasks.show', $task))
        ->assertSessionHas('success', 'Task created successfully!');
    
    // Verify BaseAction logging was called
    Log::shouldHaveReceived('debug')
        ->with('Action started: CreateTaskAction', \Mockery::type('array'))
        ->once();
    
    Log::shouldHaveReceived('debug')
        ->with('Action completed successfully: CreateTaskAction', \Mockery::type('array'))
        ->once();
}); 
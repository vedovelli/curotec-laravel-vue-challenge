<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('guests are redirected to the login page', function (): void {
    $response = $this->get('/tasks');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the tasks page', function (): void {
    $this->actingAs($this->user);

    $response = $this->get('/tasks');
    $response->assertStatus(200);
});

test('tasks page returns correct inertia response structure with task stats', function (): void {
    // Arrange
    $this->actingAs($this->user);
    Task::factory()->pending()->count(3)->create();
    Task::factory()->completed()->count(2)->create();

    // Act & Assert
    $this->get('/tasks')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks')
            ->has('currentFilter')
            ->has('taskStats')
            ->has('tasks.data', 5)
            ->has('taskStats', fn (Assert $stats) => $stats
                ->has('total_tasks')
                ->has('completed_tasks')
                ->has('pending_tasks')
                ->has('completion_percentage')
                ->where('total_tasks', 5)
                ->where('completed_tasks', 2)
                ->where('pending_tasks', 3)
                ->where('completion_percentage', 40)
            )
        );
});

test('tasks page filters work correctly', function (): void {
    // Arrange
    $this->actingAs($this->user);
    Task::factory()->pending()->count(3)->create();
    Task::factory()->completed()->count(2)->create();

    // Test pending filter
    $this->get('/tasks?status=pending')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 3)
            ->where('currentFilter', 'pending')
        );

    // Test completed filter
    $this->get('/tasks?status=completed')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 2)
            ->where('currentFilter', 'completed')
        );

    // Test all filter
    $this->get('/tasks?status=all')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 5)
            ->where('currentFilter', 'all')
        );
});

test('tasks page shows tasks in latest first order', function (): void {
    // Arrange
    $this->actingAs($this->user);
    $oldTask = Task::factory()->create(['created_at' => now()->subDays(2)]);
    $newTask = Task::factory()->create(['created_at' => now()]);

    // Act & Assert
    $this->get('/tasks')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 2)
            ->where('tasks.data.0.id', $newTask->id)
            ->where('tasks.data.1.id', $oldTask->id)
        );
});

test('tasks page pagination works correctly', function (): void {
    // Arrange
    $this->actingAs($this->user);
    Task::factory()->count(15)->create();

    // Act & Assert
    $this->get('/tasks')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 12) // Default per_page is 12
            ->has('tasks.links')
            ->where('tasks.current_page', 1)
            ->where('tasks.total', 15)
        );

    // Test second page
    $this->get('/tasks?page=2')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks.data', 3) // Remaining tasks
            ->where('tasks.current_page', 2)
        );
}); 
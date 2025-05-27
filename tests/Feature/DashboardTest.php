<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('guests are redirected to the login page', function (): void {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function (): void {
    $this->actingAs($this->user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('dashboard returns correct inertia response structure', function (): void {
    // Arrange
    $this->actingAs($this->user);
    Task::factory()->count(5)->create();

    // Act & Assert
    $this->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks')
            ->has('stats')
            ->has('pagination')
            ->has('tasks.data', 5)
            ->has('stats', fn (Assert $stats) => $stats
                ->has('total_tasks')
                ->has('completed_tasks')
                ->has('pending_tasks')
                ->has('completion_percentage')
            )
            ->has('pagination', fn (Assert $pagination) => $pagination
                ->has('current_page')
                ->has('last_page')
                ->has('per_page')
                ->has('total')
                ->has('from')
                ->has('to')
                ->has('has_more_pages')
                ->has('links')
            )
        );
});

test('dashboard includes task resource fields', function (): void {
    // Arrange
    $this->actingAs($this->user);
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'status' => Task::STATUS_PENDING,
    ]);

    // Act & Assert
    $this->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks.data.0', fn (Assert $taskData) => $taskData
                ->where('id', $task->id)
                ->where('title', 'Test Task')
                ->where('status', Task::STATUS_PENDING)
                ->has('description')
                ->has('status_text')
                ->has('due_date')
                ->has('formatted_due_date')
                ->has('is_completed')
                ->has('is_overdue')
                ->has('days_until_due')
                ->has('priority_level')
                ->has('created_at')
                ->has('updated_at')
            )
        );
});

test('dashboard paginates tasks correctly', function (): void {
    // Arrange
    $this->actingAs($this->user);
    Task::factory()->count(15)->create();

    // Act & Assert
    $this->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks.data', 10) // Default pagination is 10
            ->has('pagination')
            ->where('pagination.current_page', 1)
            ->where('pagination.last_page', 2)
            ->where('pagination.per_page', 10)
            ->where('pagination.total', 15)
            ->where('pagination.has_more_pages', true)
        );
});

test('dashboard shows tasks in latest first order', function (): void {
    // Arrange
    $this->actingAs($this->user);
    $oldTask = Task::factory()->create(['created_at' => now()->subDays(2)]);
    $newTask = Task::factory()->create(['created_at' => now()]);

    // Act & Assert
    $this->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks.data', 2)
            ->where('tasks.data.0.id', $newTask->id)
            ->where('tasks.data.1.id', $oldTask->id)
        );
});

test('dashboard uses BaseAction execute method with logging in debug mode', function (): void {
    // Arrange
    config(['app.debug' => true]);
    Log::spy();
    $this->actingAs($this->user);
    Task::factory()->count(3)->create();

    // Act
    $response = $this->get('/dashboard');

    // Assert
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks')
            ->has('stats')
            ->has('pagination')
        );
    
    // Verify BaseAction logging was called for GetTaskStatsAction
    Log::shouldHaveReceived('debug')
        ->with('Action started: GetTaskStatsAction', \Mockery::type('array'))
        ->once();
    
    Log::shouldHaveReceived('debug')
        ->with('Action completed successfully: GetTaskStatsAction', \Mockery::type('array'))
        ->once();
});
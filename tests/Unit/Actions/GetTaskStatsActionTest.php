<?php

declare(strict_types=1);

use App\Actions\GetTaskStatsAction;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = new GetTaskStatsAction(new Task);
});

it('returns correct stats when no tasks exist', function (): void {
    // Act
    $stats = $this->action->handle();

    // Assert
    expect($stats)->toBe([
        'total_tasks' => 0,
        'completed_tasks' => 0,
        'pending_tasks' => 0,
        'completion_percentage' => 0.0,
    ]);
});

it('returns correct stats with mixed task statuses', function (): void {
    // Arrange
    Task::factory()->create(['status' => 'completed']);
    Task::factory()->create(['status' => 'completed']);
    Task::factory()->create(['status' => 'pending']);
    Task::factory()->create(['status' => 'pending']);

    // Act
    $stats = $this->action->handle();

    // Assert
    expect($stats)->toBe([
        'total_tasks' => 4,
        'completed_tasks' => 2,
        'pending_tasks' => 2,
        'completion_percentage' => 50.0,
    ]);
});

it('returns correct stats with all completed tasks', function (): void {
    // Arrange
    Task::factory()->count(3)->create(['status' => 'completed']);

    // Act
    $stats = $this->action->handle();

    // Assert
    expect($stats)->toBe([
        'total_tasks' => 3,
        'completed_tasks' => 3,
        'pending_tasks' => 0,
        'completion_percentage' => 100.0,
    ]);
});

it('returns correct stats with all pending tasks', function (): void {
    // Arrange
    Task::factory()->count(5)->create(['status' => 'pending']);

    // Act
    $stats = $this->action->handle();

    // Assert
    expect($stats)->toBe([
        'total_tasks' => 5,
        'completed_tasks' => 0,
        'pending_tasks' => 5,
        'completion_percentage' => 0.0,
    ]);
});

it('calculates completion percentage correctly with decimal precision', function (): void {
    // Arrange - 1 completed out of 3 tasks = 33.33%
    Task::factory()->create(['status' => 'completed']);
    Task::factory()->create(['status' => 'pending']);
    Task::factory()->create(['status' => 'pending']);

    // Act
    $stats = $this->action->handle();

    // Assert
    expect($stats['total_tasks'])->toBe(3)
        ->and($stats['completed_tasks'])->toBe(1)
        ->and($stats['pending_tasks'])->toBe(2)
        ->and($stats['completion_percentage'])->toBe(33.33);
});

it('can be resolved from service container with dependency injection', function (): void {
    // Arrange
    Task::factory()->count(3)->create(['status' => 'completed']);
    Task::factory()->count(2)->create(['status' => 'pending']);
    
    // Act
    $action = app(GetTaskStatsAction::class);

    // Assert
    expect($action)->toBeInstanceOf(GetTaskStatsAction::class);
    
    // Verify it works with dependency injection
    $stats = $action->handle();
    
    expect($stats)->toHaveKey('total_tasks')
        ->and($stats['total_tasks'])->toBe(5)
        ->and($stats['completed_tasks'])->toBe(3)
        ->and($stats['pending_tasks'])->toBe(2)
        ->and($stats['completion_percentage'])->toBe(60.0);
}); 
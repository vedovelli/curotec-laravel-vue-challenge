<?php

declare(strict_types=1);

use App\Http\Resources\TaskStatsResource;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->statsData = [
        'total_tasks' => 10,
        'completed_tasks' => 6,
        'pending_tasks' => 4,
        'completion_percentage' => 60.0,
    ];
});

it('transforms stats array to proper structure', function (): void {
    // Arrange
    $resource = new TaskStatsResource($this->statsData);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result)->toBeArray()
        ->and($result)->toHaveKeys([
            'total_tasks',
            'completed_tasks',
            'pending_tasks',
            'completion_percentage',
        ])
        ->and($result['total_tasks'])->toBe(10)
        ->and($result['completed_tasks'])->toBe(6)
        ->and($result['pending_tasks'])->toBe(4)
        ->and($result['completion_percentage'])->toBe(60.0);
});

it('handles zero tasks correctly', function (): void {
    // Arrange
    $emptyStats = [
        'total_tasks' => 0,
        'completed_tasks' => 0,
        'pending_tasks' => 0,
        'completion_percentage' => 0.0,
    ];
    $resource = new TaskStatsResource($emptyStats);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['total_tasks'])->toBe(0)
        ->and($result['completed_tasks'])->toBe(0)
        ->and($result['pending_tasks'])->toBe(0)
        ->and($result['completion_percentage'])->toBe(0.0);
});

it('preserves decimal precision for completion percentage', function (): void {
    // Arrange
    $preciseStats = [
        'total_tasks' => 3,
        'completed_tasks' => 1,
        'pending_tasks' => 2,
        'completion_percentage' => 33.33,
    ];
    $resource = new TaskStatsResource($preciseStats);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['completion_percentage'])->toBe(33.33);
});

it('handles 100% completion correctly', function (): void {
    // Arrange
    $completeStats = [
        'total_tasks' => 5,
        'completed_tasks' => 5,
        'pending_tasks' => 0,
        'completion_percentage' => 100.0,
    ];
    $resource = new TaskStatsResource($completeStats);
    $request = new Request();

    // Act
    $result = $resource->toArray($request);

    // Assert
    expect($result['completion_percentage'])->toBe(100.0)
        ->and($result['pending_tasks'])->toBe(0)
        ->and($result['total_tasks'])->toBe($result['completed_tasks']);
}); 
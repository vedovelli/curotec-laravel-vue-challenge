<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;

it('debugs task controller response', function (): void {
    // Arrange
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'status' => 'pending',
    ]);

    // Act
    $response = $this->actingAs($user)
        ->get(route('tasks.show', $task));

    // Debug the response
    $response->assertOk();
    
    // Dump the Inertia props to see the structure
    $props = $response->viewData('page')['props'] ?? [];
    dump('Inertia Props:', $props);
    
    expect(true)->toBeTrue(); // Just to pass the test
}); 
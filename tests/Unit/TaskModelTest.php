<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

// ==========================================
// MODEL INSTANTIATION AND PROPERTY TESTS
// ==========================================

test('model instantiation using factory', function () {
    $task = Task::factory()->create();

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->exists)->toBeTrue()
        ->and($task->id)->not->toBeNull();
});

test('model instantiation with make using factory', function () {
    $task = Task::factory()->make();

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->exists)->toBeFalse()
        ->and($task->id)->toBeNull();
});

test('model property access', function () {
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'status' => 'pending',
        'due_date' => Carbon::tomorrow(),
    ]);

    expect($task->title)->toBe('Test Task')
        ->and($task->description)->toBe('Test Description')
        ->and($task->status)->toBe('pending')
        ->and($task->due_date)->toBeInstanceOf(Carbon::class)
        ->and($task->created_at)->toBeInstanceOf(Carbon::class)
        ->and($task->updated_at)->toBeInstanceOf(Carbon::class);
});

// ==========================================
// FILLABLE PROPERTIES AND MASS ASSIGNMENT TESTS
// ==========================================

test('fillable properties', function () {
    $fillableAttributes = [
        'title' => 'Fillable Test Task',
        'description' => 'Fillable test description',
        'status' => 'completed',
        'due_date' => Carbon::tomorrow(),
    ];

    $task = Task::factory()->create($fillableAttributes);

    expect($task->title)->toBe('Fillable Test Task')
        ->and($task->description)->toBe('Fillable test description')
        ->and($task->status)->toBe('completed')
        ->and($task->due_date->format('Y-m-d'))->toBe(Carbon::tomorrow()->format('Y-m-d'));
});

test('mass assignment protection', function () {
    $task = new Task();
    $fillable = $task->getFillable();

    $expectedFillable = ['title', 'description', 'status', 'due_date'];
    expect($fillable)->toBe($expectedFillable);
});

// ==========================================
// MODEL CONSTANTS TESTS
// ==========================================

test('status constants', function () {
    expect(Task::STATUS_PENDING)->toBe('pending')
        ->and(Task::STATUS_COMPLETED)->toBe('completed');
});

test('statuses array constant', function () {
    $expectedStatuses = ['pending', 'completed'];
    expect(Task::STATUSES)->toBe($expectedStatuses);
});

test('status constants are in statuses array', function () {
    expect(Task::STATUSES)->toContain(Task::STATUS_PENDING)
        ->and(Task::STATUSES)->toContain(Task::STATUS_COMPLETED);
});

// ==========================================
// CUSTOM METHODS TESTS
// ==========================================

test('mark as completed method', function () {
    $task = Task::factory()->pending()->create();
    expect($task->status)->toBe('pending');

    $result = $task->markAsCompleted();

    expect($result)->toBeTrue()
        ->and($task->status)->toBe('completed')
        ->and($task->fresh()->status)->toBe('completed');
});

test('mark as pending method', function () {
    $task = Task::factory()->completed()->create();
    expect($task->status)->toBe('completed');

    $result = $task->markAsPending();

    expect($result)->toBeTrue()
        ->and($task->status)->toBe('pending')
        ->and($task->fresh()->status)->toBe('pending');
});

test('is completed method', function () {
    $completedTask = Task::factory()->completed()->create();
    $pendingTask = Task::factory()->pending()->create();

    expect($completedTask->isCompleted())->toBeTrue()
        ->and($pendingTask->isCompleted())->toBeFalse();
});

test('is pending method', function () {
    $pendingTask = Task::factory()->pending()->create();
    $completedTask = Task::factory()->completed()->create();

    expect($pendingTask->isPending())->toBeTrue()
        ->and($completedTask->isPending())->toBeFalse();
});

test('is overdue method with overdue task', function () {
    $overdueTask = Task::factory()->overdue()->create();

    expect($overdueTask->isOverdue())->toBeTrue();
});

test('is overdue method with future due date', function () {
    $futureTask = Task::factory()->pending()->create([
        'due_date' => Carbon::tomorrow(),
    ]);

    expect($futureTask->isOverdue())->toBeFalse();
});

test('is overdue method with no due date', function () {
    $taskWithoutDueDate = Task::factory()->pending()->withoutDueDate()->create();

    expect($taskWithoutDueDate->isOverdue())->toBeFalse();
});

test('is overdue method with completed task', function () {
    $completedOverdueTask = Task::factory()->completed()->create([
        'due_date' => Carbon::yesterday(),
    ]);

    expect($completedOverdueTask->isOverdue())->toBeFalse();
});

// ==========================================
// QUERY SCOPES TESTS
// ==========================================

test('pending scope', function () {
    Task::factory()->pending()->count(3)->create();
    Task::factory()->completed()->count(2)->create();

    $pendingTasks = Task::pending()->get();

    expect($pendingTasks)->toHaveCount(3);
    foreach ($pendingTasks as $task) {
        expect($task->status)->toBe('pending');
    }
});

test('completed scope', function () {
    Task::factory()->pending()->count(2)->create();
    Task::factory()->completed()->count(3)->create();

    $completedTasks = Task::completed()->get();

    expect($completedTasks)->toHaveCount(3);
    foreach ($completedTasks as $task) {
        expect($task->status)->toBe('completed');
    }
});

test('overdue scope', function () {
    // Create overdue tasks (pending with past due dates)
    Task::factory()->overdue()->count(2)->create();
    
    // Create non-overdue tasks
    Task::factory()->pending()->count(1)->create(['due_date' => Carbon::tomorrow()]);
    Task::factory()->completed()->count(1)->create(['due_date' => Carbon::yesterday()]);
    Task::factory()->pending()->withoutDueDate()->count(1)->create();

    $overdueTasks = Task::overdue()->get();

    expect($overdueTasks)->toHaveCount(2);
    foreach ($overdueTasks as $task) {
        expect($task->status)->toBe('pending')
            ->and($task->due_date)->not->toBeNull()
            ->and($task->due_date->isPast())->toBeTrue();
    }
});

test('due today scope', function () {
    // Create tasks due today
    Task::factory()->count(2)->create(['due_date' => today()]);
    
    // Create tasks not due today
    Task::factory()->count(1)->create(['due_date' => Carbon::tomorrow()]);
    Task::factory()->count(1)->create(['due_date' => Carbon::yesterday()]);

    $tasksDueToday = Task::dueToday()->get();

    expect($tasksDueToday)->toHaveCount(2);
    foreach ($tasksDueToday as $task) {
        expect($task->due_date->format('Y-m-d'))->toBe(today()->format('Y-m-d'));
    }
});

test('due within scope', function () {
    // Create tasks due within 7 days
    Task::factory()->count(2)->create(['due_date' => now()->addDays(3)]);
    Task::factory()->count(1)->create(['due_date' => now()->addDays(6)]);
    
    // Create tasks outside the range
    Task::factory()->count(1)->create(['due_date' => now()->addDays(10)]);
    Task::factory()->count(1)->create(['due_date' => now()->subDays(1)]);

    $tasksDueWithin7Days = Task::dueWithin(7)->get();

    expect($tasksDueWithin7Days)->toHaveCount(3);
    foreach ($tasksDueWithin7Days as $task) {
        expect($task->due_date)->not->toBeNull()
            ->and($task->due_date)->toBeGreaterThanOrEqual(now())
            ->and($task->due_date)->toBeLessThanOrEqual(now()->addDays(7));
    }
});

// ==========================================
// ACCESSORS TESTS
// ==========================================

test('status text accessor', function () {
    $pendingTask = Task::factory()->pending()->create();
    $completedTask = Task::factory()->completed()->create();

    expect($pendingTask->status_text)->toBe('Pending')
        ->and($completedTask->status_text)->toBe('Completed');
});

test('is overdue accessor', function () {
    $overdueTask = Task::factory()->overdue()->create();
    $futureTask = Task::factory()->pending()->create(['due_date' => Carbon::tomorrow()]);

    expect($overdueTask->is_overdue)->toBeTrue()
        ->and($futureTask->is_overdue)->toBeFalse();
});

test('is completed accessor', function () {
    $completedTask = Task::factory()->completed()->create();
    $pendingTask = Task::factory()->pending()->create();

    expect($completedTask->is_completed)->toBeTrue()
        ->and($pendingTask->is_completed)->toBeFalse();
});

test('days until due accessor', function () {
    $taskDueTomorrow = Task::factory()->create(['due_date' => Carbon::tomorrow()->startOfDay()]);
    $taskDueIn5Days = Task::factory()->create(['due_date' => now()->addDays(5)->startOfDay()]);
    $overdueTask = Task::factory()->overdue()->create();
    $taskWithoutDueDate = Task::factory()->withoutDueDate()->create();

    // Use more flexible assertions due to timing precision
    expect($taskDueTomorrow->days_until_due)->toBeGreaterThanOrEqual(0)
        ->and($taskDueTomorrow->days_until_due)->toBeLessThanOrEqual(1)
        ->and($taskDueIn5Days->days_until_due)->toBeGreaterThanOrEqual(4)
        ->and($taskDueIn5Days->days_until_due)->toBeLessThanOrEqual(5)
        ->and($overdueTask->days_until_due)->toBeLessThan(0)
        ->and($taskWithoutDueDate->days_until_due)->toBeNull();
});

test('formatted due date accessor', function () {
    $task = Task::factory()->create(['due_date' => Carbon::parse('2024-12-25')]);
    $taskWithoutDueDate = Task::factory()->withoutDueDate()->create();

    expect($task->formatted_due_date)->toBe('Dec 25, 2024')
        ->and($taskWithoutDueDate->formatted_due_date)->toBeNull();
});

test('priority level accessor', function () {
    $overdueTask = Task::factory()->overdue()->create();
    $dueTodayTask = Task::factory()->pending()->create(['due_date' => now()->endOfDay()]);
    $dueTomorrowTask = Task::factory()->pending()->create(['due_date' => now()->addDay()->endOfDay()]);
    $dueIn3DaysTask = Task::factory()->pending()->create(['due_date' => now()->addDays(3)]);
    $dueIn5DaysTask = Task::factory()->pending()->create(['due_date' => now()->addDays(5)]);
    $futureTask = Task::factory()->pending()->create(['due_date' => now()->addDays(10)]);
    $taskWithoutDueDate = Task::factory()->pending()->withoutDueDate()->create();
    $completedTask = Task::factory()->completed()->create();

    expect($overdueTask->priority_level)->toBe('overdue')
        ->and($dueTodayTask->priority_level)->toBe('urgent')
        ->and($dueTomorrowTask->priority_level)->toBe('urgent')
        ->and($dueIn3DaysTask->priority_level)->toBe('high')
        ->and($dueIn5DaysTask->priority_level)->toBe('medium')
        ->and($futureTask->priority_level)->toBe('normal')
        ->and($taskWithoutDueDate->priority_level)->toBe('normal')
        ->and($completedTask->priority_level)->toBe('completed');
});

// ==========================================
// ATTRIBUTE CASTING TESTS
// ==========================================

test('due date casting', function () {
    $task = Task::factory()->create(['due_date' => '2024-12-25 10:30:00']);

    expect($task->due_date)->toBeInstanceOf(Carbon::class)
        ->and($task->due_date->format('Y-m-d'))->toBe('2024-12-25')
        ->and($task->due_date->format('H:i:s'))->toBe('10:30:00');
});

test('created at and updated at casting', function () {
    $task = Task::factory()->create();

    expect($task->created_at)->toBeInstanceOf(Carbon::class)
        ->and($task->updated_at)->toBeInstanceOf(Carbon::class);
});

test('nullable due date casting', function () {
    $task = Task::factory()->withoutDueDate()->create();

    expect($task->due_date)->toBeNull();
});

// ==========================================
// MODEL RELATIONSHIPS TESTS
// ==========================================

test('user relationship removed', function () {
    $task = Task::factory()->make();
    
    // Verify that user relationship method doesn't exist
    expect(method_exists($task, 'user'))->toBeFalse();
    
    // Note: User relationship was removed as no user_id column exists in tasks table.
    // When user association is needed, add user_id column and restore the relationship.
});

// ==========================================
// MODEL BEHAVIOR TESTS
// ==========================================

test('model is final class', function () {
    $reflection = new \ReflectionClass(Task::class);
    expect($reflection->isFinal())->toBeTrue();
});

test('model uses has factory trait', function () {
    $task = new Task();
    expect(method_exists($task, 'factory'))->toBeTrue();
});

test('model table name', function () {
    $task = new Task();
    expect($task->getTable())->toBe('tasks');
});

test('model primary key', function () {
    $task = new Task();
    expect($task->getKeyName())->toBe('id');
});

test('model timestamps', function () {
    $task = new Task();
    expect($task->usesTimestamps())->toBeTrue();
});

// ==========================================
// EDGE CASES AND ERROR HANDLING TESTS
// ==========================================

test('model with invalid status fails database constraint', function () {
    // The database has an enum constraint that prevents invalid status values
    $task = Task::factory()->make(['status' => 'invalid_status']);
    
    expect(fn() => $task->save())->toThrow(\Illuminate\Database\QueryException::class);
});

test('model with null required fields', function () {
    $task = new Task();
    
    // This should fail at the database level due to NOT NULL constraints
    expect(fn() => $task->save())->toThrow(\Illuminate\Database\QueryException::class);
});

test('model updates timestamps on save', function () {
    $task = Task::factory()->create();
    $originalUpdatedAt = $task->updated_at;

    // Wait a moment to ensure timestamp difference
    sleep(1);
    
    $task->title = 'Updated Title';
    $task->save();

    expect($task->updated_at)->not->toBe($originalUpdatedAt)
        ->and($task->updated_at)->toBeGreaterThan($originalUpdatedAt);
});

// ==========================================
// COMPLEX SCENARIO TESTS
// ==========================================

test('task lifecycle scenario', function () {
    // Create a pending task
    $task = Task::factory()->pending()->create([
        'title' => 'Lifecycle Test Task',
        'due_date' => Carbon::tomorrow(),
    ]);

    // Verify initial state
    expect($task->isPending())->toBeTrue()
        ->and($task->isCompleted())->toBeFalse()
        ->and($task->isOverdue())->toBeFalse();

    // Mark as completed
    $task->markAsCompleted();

    // Verify completed state
    expect($task->isCompleted())->toBeTrue()
        ->and($task->isPending())->toBeFalse()
        ->and($task->isOverdue())->toBeFalse(); // Completed tasks are never overdue
});

test('overdue task scenario', function () {
    // Create an overdue task
    $task = Task::factory()->overdue()->create();

    // Verify overdue state
    expect($task->isPending())->toBeTrue()
        ->and($task->isOverdue())->toBeTrue()
        ->and($task->priority_level)->toBe('overdue');

    // Complete the overdue task
    $task->markAsCompleted();

    // Verify it's no longer considered overdue
    expect($task->isOverdue())->toBeFalse()
        ->and($task->isCompleted())->toBeTrue()
        ->and($task->priority_level)->toBe('completed');
});

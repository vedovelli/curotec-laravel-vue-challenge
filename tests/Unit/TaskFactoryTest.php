<?php

declare(strict_types=1);

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

    // ==========================================
    // BASIC FACTORY FUNCTIONALITY TESTS
    // ==========================================

test('factory creates valid task instance', function () {
    $task = Task::factory()->create();

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->id)->not->toBeNull()
        ->and($task->title)->not->toBeNull()
        ->and($task->status)->not->toBeNull()
        ->and($task->created_at)->not->toBeNull()
        ->and($task->updated_at)->not->toBeNull();
});

test('factory make vs create methods', function () {
    // Test make() - doesn't persist to database
    $madeTask = Task::factory()->make();
    expect($madeTask)->toBeInstanceOf(Task::class)
        ->and($madeTask->id)->toBeNull();
    
    $this->assertDatabaseCount('tasks', 0);

    // Test create() - persists to database
    $createdTask = Task::factory()->create();
    expect($createdTask)->toBeInstanceOf(Task::class)
        ->and($createdTask->id)->not->toBeNull();
    
    $this->assertDatabaseCount('tasks', 1);
});

test('factory creates multiple instances', function () {
    $tasks = Task::factory()->count(5)->create();

    expect($tasks)->toHaveCount(5);
    $this->assertDatabaseCount('tasks', 5);

    foreach ($tasks as $task) {
        expect($task)->toBeInstanceOf(Task::class)
            ->and($task->id)->not->toBeNull();
    }
});

test('factory with custom attributes override', function () {
    $customTitle = 'Custom Task Title';
    $customDescription = 'Custom task description';

    $task = Task::factory()->create([
        'title' => $customTitle,
        'description' => $customDescription,
    ]);

    expect($task->title)->toBe($customTitle)
        ->and($task->description)->toBe($customDescription);
});

    // ==========================================
    // DATA GENERATION TESTS
    // ==========================================

    test('factory generates valid title', function () {
        $task = Task::factory()->create();

        expect($task->title)->toBeString()
            ->not->toBeEmpty()
            ->and(strlen($task->title))->toBeLessThanOrEqual(255);
    });

    test('factory generates optional description', function () {
        $tasks = Task::factory()->count(10)->create();

        $hasDescription = $tasks->filter(fn($task) => !is_null($task->description))->count();

        // Should have some with descriptions (due to optional(0.8))
        expect($hasDescription)->toBeGreaterThan(0);
    });

    test('factory generates valid status', function () {
        $tasks = Task::factory()->count(20)->create();

        foreach ($tasks as $task) {
            expect($task->status)->toBeIn(['pending', 'completed']);
        }

        // Should have both statuses represented in a larger sample
        $statuses = $tasks->pluck('status')->unique();
        expect($statuses->count())->toBeGreaterThan(1);
    });

    test('factory generates optional due date', function () {
        $tasks = Task::factory()->count(10)->create();

        $hasDueDate = $tasks->filter(fn($task) => !is_null($task->due_date))->count();

        // Should have some with due dates (due to optional(0.6))
        expect($hasDueDate)->toBeGreaterThan(0);
    });

    test('factory generates future due dates', function () {
        $tasks = Task::factory()->count(10)->create();

        $tasksWithDueDate = $tasks->filter(fn($task) => !is_null($task->due_date));

        foreach ($tasksWithDueDate as $task) {
            expect($task->due_date)->toBeInstanceOf(Carbon::class)
                ->and($task->due_date)->toBeGreaterThanOrEqual(now()->startOfDay())
                ->and($task->due_date)->toBeLessThanOrEqual(now()->addDays(30));
        }
    });

    // ==========================================
    // FACTORY STATES TESTS
    // ==========================================

    test('pending state', function () {
        $task = Task::factory()->pending()->create();

        expect($task->status)->toBe('pending')
            ->and($task->isPending())->toBeTrue()
            ->and($task->isCompleted())->toBeFalse();

        // Due date should be in the future if present
        if ($task->due_date) {
            expect($task->due_date)->toBeGreaterThanOrEqual(now()->startOfDay())
                ->and($task->due_date)->toBeLessThanOrEqual(now()->addDays(14));
        }
    });

    test('completed state', function () {
        $task = Task::factory()->completed()->create();

        expect($task->status)->toBe('completed')
            ->and($task->isCompleted())->toBeTrue()
            ->and($task->isPending())->toBeFalse();

        // Due date should be in the past if present
        if ($task->due_date) {
            expect($task->due_date)->toBeLessThanOrEqual(now())
                ->and($task->due_date)->toBeGreaterThanOrEqual(now()->subDays(30));
        }
    });

    test('overdue state', function () {
        $task = Task::factory()->overdue()->create();

        expect($task->status)->toBe('pending')
            ->and($task->due_date)->not->toBeNull()
            ->and($task->due_date->isPast())->toBeTrue()
            ->and($task->isOverdue())->toBeTrue()
            ->and($task->due_date)->toBeGreaterThanOrEqual(now()->subDays(14))
            ->and($task->due_date)->toBeLessThan(now());
    });

    test('without due date state', function () {
        $task = Task::factory()->withoutDueDate()->create();

        expect($task->due_date)->toBeNull()
            ->and($task->isOverdue())->toBeFalse();
    });

    test('due on specific date state', function () {
        $specificDate = Carbon::parse('2024-12-25 10:00:00');
        $task = Task::factory()->dueOn($specificDate)->create();

        expect($task->due_date)->not->toBeNull()
            ->and($task->due_date->format('Y-m-d H:i:s'))->toBe($specificDate->format('Y-m-d H:i:s'));
    });

    test('due on string date state', function () {
        $dateString = '2024-12-31';
        $task = Task::factory()->dueOn($dateString)->create();

        expect($task->due_date)->not->toBeNull()
            ->and($task->due_date->format('Y-m-d'))->toBe($dateString);
    });

    test('with long description state', function () {
        $task = Task::factory()->withLongDescription()->create();

        expect($task->description)->not->toBeNull()
            ->and(strlen($task->description))->toBeGreaterThan(100);
    });

    test('without description state', function () {
        $task = Task::factory()->withoutDescription()->create();

        expect($task->description)->toBeNull();
    });

    // ==========================================
    // FACTORY SEQUENCE AND BATCH TESTS
    // ==========================================

    test('factory sequence generation', function () {
        $tasks = Task::factory()
            ->count(3)
            ->sequence(
                ['status' => 'pending'],
                ['status' => 'completed'],
                ['status' => 'pending']
            )
            ->create();

        expect($tasks[0]->status)->toBe('pending')
            ->and($tasks[1]->status)->toBe('completed')
            ->and($tasks[2]->status)->toBe('pending');
    });

    test('factory state combinations', function () {
        $task = Task::factory()
            ->pending()
            ->withoutDescription()
            ->dueOn(Carbon::tomorrow())
            ->create();

        expect($task->status)->toBe('pending')
            ->and($task->description)->toBeNull()
            ->and($task->due_date->format('Y-m-d'))->toBe(Carbon::tomorrow()->format('Y-m-d'));
    });

    // ==========================================
    // DATA CONSTRAINT VALIDATION TESTS
    // ==========================================

    test('factory generates data meeting model constraints', function () {
        $tasks = Task::factory()->count(10)->create();

        foreach ($tasks as $task) {
            // Title constraints
            expect($task->title)->toBeString()
                ->not->toBeEmpty()
                ->and(strlen($task->title))->toBeLessThanOrEqual(255);

            // Status constraints
            expect($task->status)->toBeIn(Task::STATUSES);

            // Description constraints (nullable)
            if ($task->description !== null) {
                expect($task->description)->toBeString();
            }

            // Due date constraints (nullable, datetime)
            if ($task->due_date !== null) {
                expect($task->due_date)->toBeInstanceOf(Carbon::class);
            }

            // Timestamps should be present
            expect($task->created_at)->toBeInstanceOf(Carbon::class)
                ->and($task->updated_at)->toBeInstanceOf(Carbon::class);
        }
    });

    test('factory respects fillable attributes', function () {
        $task = Task::factory()->create([
            'title' => 'Test Title',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => Carbon::tomorrow(),
        ]);

        expect($task->title)->toBe('Test Title')
            ->and($task->description)->toBe('Test Description')
            ->and($task->status)->toBe('pending')
            ->and($task->due_date->format('Y-m-d'))->toBe(Carbon::tomorrow()->format('Y-m-d'));
    });

    // ==========================================
    // RELATIONSHIP TESTS (PREPARED FOR FUTURE)
    // ==========================================

    test('factory can create task without user relationship', function () {
        $task = Task::factory()->create();

        // Currently, tasks don't require a user relationship
        expect($task)->toBeInstanceOf(Task::class)
            ->and($task->id)->not->toBeNull();
        
        // This test is prepared for when user relationships are implemented
        // For now, we just ensure the task can be created without issues
    });

    // ==========================================
    // EDGE CASE TESTS
    // ==========================================

    test('factory handles empty custom attributes', function () {
        $task = Task::factory()->create([]);

        expect($task)->toBeInstanceOf(Task::class)
            ->and($task->title)->not->toBeNull()
            ->and($task->status)->not->toBeNull();
    });

    test('factory handles partial custom attributes', function () {
        $task = Task::factory()->create([
            'title' => 'Partial Override',
        ]);

        expect($task->title)->toBe('Partial Override')
            ->and($task->status)->not->toBeNull(); // Should still be generated
    });

    test('factory title generation variety', function () {
        $tasks = Task::factory()->count(20)->create();
        $titles = $tasks->pluck('title')->unique();

            // Should generate varied titles (at least 10 different ones in 20 tasks)
    expect($titles->count())->toBeGreaterThan(10);
});

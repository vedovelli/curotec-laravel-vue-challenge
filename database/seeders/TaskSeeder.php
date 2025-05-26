<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class TaskSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed in non-production environments
        if (app()->environment('production')) {
            $this->command->info('Skipping TaskSeeder in production environment');
            return;
        }

        $this->command->info('Seeding tasks...');

        // Clear existing tasks if any
        Task::query()->delete();

        // Create a variety of tasks for development
        $this->createPendingTasks();
        $this->createCompletedTasks();
        $this->createOverdueTasks();
        $this->createTasksWithoutDueDates();
        $this->createTasksWithVariedDescriptions();

        $totalTasks = Task::count();
        $this->command->info("Created {$totalTasks} sample tasks successfully!");
    }

    /**
     * Create pending tasks with future due dates.
     */
    private function createPendingTasks(): void
    {
        Task::factory()
            ->pending()
            ->count(8)
            ->create();

        $this->command->info('✓ Created 8 pending tasks');
    }

    /**
     * Create completed tasks.
     */
    private function createCompletedTasks(): void
    {
        Task::factory()
            ->completed()
            ->count(5)
            ->create();

        $this->command->info('✓ Created 5 completed tasks');
    }

    /**
     * Create overdue tasks for testing overdue functionality.
     */
    private function createOverdueTasks(): void
    {
        Task::factory()
            ->overdue()
            ->count(3)
            ->create();

        $this->command->info('✓ Created 3 overdue tasks');
    }

    /**
     * Create tasks without due dates.
     */
    private function createTasksWithoutDueDates(): void
    {
        Task::factory()
            ->withoutDueDate()
            ->count(2)
            ->create();

        $this->command->info('✓ Created 2 tasks without due dates');
    }

    /**
     * Create tasks with varied description lengths.
     */
    private function createTasksWithVariedDescriptions(): void
    {
        // Tasks with long descriptions
        Task::factory()
            ->pending()
            ->withLongDescription()
            ->count(2)
            ->create();

        // Tasks without descriptions
        Task::factory()
            ->pending()
            ->withoutDescription()
            ->count(2)
            ->create();

        $this->command->info('✓ Created 4 tasks with varied descriptions');
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
final class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->generateTaskTitle(),
            'description' => fake()->optional(0.8)->paragraph(),
            'status' => fake()->randomElement(['pending', 'completed']),
            'due_date' => fake()->optional(0.6)->dateTimeBetween('now', '+30 days'),
        ];
    }

    /**
     * Generate realistic task titles.
     */
    private function generateTaskTitle(): string
    {
        $taskTypes = [
            'Fix bug in',
            'Implement',
            'Update',
            'Create',
            'Review',
            'Test',
            'Deploy',
            'Optimize',
            'Refactor',
            'Document',
        ];

        $subjects = [
            'user authentication system',
            'payment processing',
            'email notifications',
            'database schema',
            'API endpoints',
            'frontend components',
            'security measures',
            'performance metrics',
            'user interface',
            'data validation',
            'error handling',
            'search functionality',
            'file upload feature',
            'reporting dashboard',
            'mobile responsiveness',
        ];

        $taskType = fake()->randomElement($taskTypes);
        $subject = fake()->randomElement($subjects);

        return "{$taskType} {$subject}";
    }

    /**
     * Create a task with pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+14 days'),
        ]);
    }

    /**
     * Create a task with completed status.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'due_date' => fake()->optional(0.5)->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a task that is overdue (past due date with pending status).
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'due_date' => fake()->dateTimeBetween('-14 days', '-1 day'),
        ]);
    }

    /**
     * Create a task with no due date.
     */
    public function withoutDueDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => null,
        ]);
    }

    /**
     * Create a task with a specific due date.
     */
    public function dueOn(Carbon|string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $date,
        ]);
    }

    /**
     * Create a task with a long description.
     */
    public function withLongDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraphs(3, true),
        ]);
    }

    /**
     * Create a task without description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}

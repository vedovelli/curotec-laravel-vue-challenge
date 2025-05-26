# Actions Pattern Documentation

## Overview

The Actions pattern is a design approach that encapsulates business logic into single-purpose, reusable classes. Each action represents a specific operation or use case in the application.

## Key Principles

1. **Single Responsibility**: Each action should do one thing well
2. **Immutability**: Actions should be read-only (no property mutations)
3. **Type Safety**: Proper type declarations for all methods
4. **Error Handling**: Consistent error handling across all actions
5. **Testability**: Easy to unit test in isolation

## Architecture

### ActionInterface

The `ActionInterface` defines the contract that all action classes must implement:

```php
interface ActionInterface
{
    public function handle(mixed $input = null): mixed;
}
```

### BaseAction

The `BaseAction` abstract class provides common functionality for all actions:

- **Error handling and logging**: Automatic logging of action execution
- **Input validation helpers**: Common validation methods
- **Consistent structure**: Enforces the action pattern

## Usage

### Creating a New Action

1. Create a new class extending `BaseAction`
2. Make the class `final` to prevent inheritance
3. Implement the `handle()` method with proper type declarations
4. Use dependency injection via constructor for external dependencies

```php
<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Actions\BaseAction;
use App\Models\Task;

final class CreateTaskAction extends BaseAction
{
    public function __construct(
        private readonly Task $taskModel
    ) {}

    public function handle(array $data): Task
    {
        $this->validateArrayInput($data, ['title', 'status']);

        return $this->taskModel->create($data);
    }
}
```

### Using Actions

Actions can be used in controllers, commands, jobs, or other services:

```php
// In a controller
public function store(CreateTaskRequest $request, CreateTaskAction $action): JsonResponse
{
    $task = $action->handle($request->validated());

    return response()->json($task, 201);
}

// With error handling
public function store(CreateTaskRequest $request, CreateTaskAction $action): JsonResponse
{
    try {
        $task = $action->execute($request->validated()); // Uses execute() for logging
        return response()->json($task, 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create task'], 500);
    }
}
```

## Available Validation Helpers

The `BaseAction` class provides several validation helpers:

### validateInputNotNull()

```php
$this->validateInputNotNull($input, 'Custom error message');
```

### validateInputType()

```php
$this->validateInputType($input, 'array', 'Input must be an array');
```

### validateArrayInput()

```php
$this->validateArrayInput($input, ['required_key1', 'required_key2']);
```

## Logging

Actions automatically log their execution when `app.debug` is enabled:

- **Start**: Logs when action begins execution
- **Success**: Logs when action completes successfully
- **Error**: Logs when action throws an exception

Use the `execute()` method instead of `handle()` to enable automatic logging:

```php
$result = $action->execute($input); // With logging
$result = $action->handle($input);  // Without logging
```

## Testing

Actions are designed to be easily testable:

```php
it('creates a task successfully', function () {
    $action = new CreateTaskAction(new Task());
    $data = ['title' => 'Test Task', 'status' => 'pending'];

    $result = $action->handle($data);

    expect($result)->toBeInstanceOf(Task::class);
    expect($result->title)->toBe('Test Task');
});
```

## Best Practices

1. **Keep actions focused**: One action per use case
2. **Use type hints**: Always declare parameter and return types
3. **Validate inputs**: Use the provided validation helpers
4. **Handle errors gracefully**: Let exceptions bubble up or handle them appropriately
5. **Write tests**: Each action should have comprehensive unit tests
6. **Use dependency injection**: Inject dependencies via constructor
7. **Make classes final**: Prevent inheritance to maintain integrity

## Directory Structure

```
app/Actions/
├── Contracts/
│   └── ActionInterface.php
├── BaseAction.php
├── Tasks/
│   ├── CreateTaskAction.php
│   ├── UpdateTaskAction.php
│   ├── DeleteTaskAction.php
│   └── GetTaskStatsAction.php
└── README.md
```

## Service Container Registration

Register actions in a service provider for dependency injection:

```php
// In AppServiceProvider or dedicated ActionServiceProvider
public function register(): void
{
    $this->app->bind(CreateTaskAction::class, function ($app) {
        return new CreateTaskAction($app->make(Task::class));
    });
}
```

## Error Handling

Actions should throw meaningful exceptions:

```php
public function handle(array $data): Task
{
    if (empty($data['title'])) {
        throw new \InvalidArgumentException('Task title is required');
    }

    // ... rest of the logic
}
```

This approach ensures consistent error handling across the application and makes debugging easier.

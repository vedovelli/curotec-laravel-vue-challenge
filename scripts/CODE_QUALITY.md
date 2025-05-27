# Code Quality Setup

This project uses ESLint, Prettier, and PHPStan to maintain consistent code quality and formatting.

## Available Scripts

### Linting

- `npm run lint` - Run ESLint with auto-fix on the frontend code
- `npm run lint:check` - Check for linting issues without fixing them

### Formatting

- `npm run format` - Format all frontend code using Prettier
- `npm run format:check` - Check if code is properly formatted

### Static Analysis

- `composer quality` - Run PHPStan static analysis on the backend code
- `vendor/bin/phpstan analyse` - Run PHPStan directly with default configuration

## Configuration Files

- `eslint.config.js` - ESLint configuration using the flat config format
- `.prettierrc` - Prettier configuration for consistent formatting
- `.editorconfig` - Editor configuration for consistent settings across IDEs
- `phpstan.neon` - PHPStan configuration for static analysis (level 6)

## Project Structure

### Frontend (`resources/js/`)

```
resources/js/
├── components/          # Reusable Vue components
│   ├── common/         # Common/shared components
│   ├── forms/          # Form-specific components (e.g., TaskForm)
│   └── ui/             # UI library components
├── composables/        # Vue composables
├── constants/          # Application constants
├── layouts/            # Page layouts
├── pages/              # Page components
├── stores/             # Pinia stores
├── types/              # TypeScript type definitions
└── utils/              # Utility functions
```

### Backend (`app/`)

```
app/
├── Actions/            # Business logic actions (extend BaseAction)
├── Http/
│   ├── Controllers/    # Single-action HTTP controllers
│   ├── Middleware/     # HTTP middleware
│   ├── Requests/       # Form request validation
│   ├── Resources/      # API resources
│   └── Traits/         # Reusable controller traits
├── Models/             # Eloquent models with scopes
├── Repositories/       # Data access layer
└── Services/           # Service layer
```

## Architectural Patterns

### BaseAction Pattern

All business logic actions extend the `BaseAction` class for consistency and optional logging:

```php
// ✅ DO: Extend BaseAction
class CreateTaskAction extends BaseAction
{
    public function handle(array $data): Task
    {
        return Task::create($data);
    }
}

// ✅ DO: Use execute() method in controllers for optional logging
$task = $action->execute($request->validated());

// ✅ ALSO OK: Use handle() method directly
$task = $action->handle($request->validated());
```

**Benefits:**

- Consistent action structure across the application
- Optional logging capabilities via `execute()` method
- Easier testing with standardized base class
- Future extensibility for cross-cutting concerns

### TaskForm Component Pattern

Reusable form components that accept props and emit events:

```vue
<!-- ✅ DO: Create reusable form components -->
<template>
  <TaskForm :form="form" :mode="'create'" @submit="handleSubmit" />
</template>

<script setup lang="ts">
// ✅ DO: Define clear interfaces for form components
interface TaskFormProps {
  form: TaskFormData;
  mode: 'create' | 'edit';
}

// ✅ DO: Use emit for form submission
const emit = defineEmits<{
  submit: [data: TaskFormData];
}>();
</script>
```

**Benefits:**

- Eliminates code duplication between Create/Edit pages
- Consistent form behavior and validation
- Easier maintenance and testing
- Clear separation of concerns

### Semantic Color System

Use semantic color variables instead of hardcoded Tailwind colors:

```vue
<!-- ✅ DO: Use semantic color variables -->
<div class="text-foreground bg-background border-border">
  <h1 class="text-foreground">Title</h1>
  <p class="text-muted-foreground">Description</p>
  <button class="text-primary hover:text-primary/80">Action</button>
  <span class="text-destructive">Error message</span>
</div>

<!-- ❌ DON'T: Use hardcoded colors -->
<div class="border-gray-300 bg-white text-gray-800">
  <h1 class="text-gray-900">Title</h1>
  <p class="text-gray-600">Description</p>
  <button class="text-blue-600 hover:text-blue-500">Action</button>
  <span class="text-red-600">Error message</span>
</div>
```

**Available Semantic Colors:**

- `text-foreground` / `bg-background` - Primary text/background
- `text-muted-foreground` / `bg-muted` - Secondary text/background
- `border-border` - Standard borders
- `text-primary` - Primary action color
- `text-destructive` - Error/danger color
- `text-success` - Success color
- `text-warning` - Warning color

### Controller Traits Pattern

Extract common controller logic into reusable traits:

```php
// ✅ DO: Use traits for common controller functionality
class ListTasksController
{
    use TransformsPagination;

    public function __invoke(Request $request): JsonResponse
    {
        $tasks = Task::with('user')->paginate(10);

        return response()->json(
            $this->transformPagination($tasks, TaskResource::class)
        );
    }
}
```

**Benefits:**

- Reduces code duplication across controllers
- Consistent pagination structure
- Easier maintenance of common functionality

### Model Scopes Pattern

Use Eloquent scopes for readable and reusable query logic:

```php
// ✅ DO: Use scopes in actions and controllers
class GetTaskStatsAction extends BaseAction
{
    public function handle(): array
    {
        return [
            'total' => Task::count(),
            'pending' => Task::pending()->count(),
            'completed' => Task::completed()->count(),
        ];
    }
}

// ✅ DO: Define clear, descriptive scopes
class Task extends Model
{
    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }
}
```

## Code Quality Rules

### Frontend

- Vue 3 Composition API preferred
- TypeScript strict mode enabled
- Consistent formatting with Prettier
- ESLint rules for Vue 3 best practices
- Automatic import organization
- TailwindCSS class sorting
- **Use semantic color variables instead of hardcoded colors**
- **Create reusable form components for common patterns**
- **Define clear TypeScript interfaces for component props**

### Backend

- **All actions must extend BaseAction**
- **Use execute() method in controllers for optional logging**
- **Single-action controllers only**
- **Extract common controller logic into traits**
- **Use Eloquent scopes for readable queries**
- **Maintain consistent method signatures across similar actions**
- **Write proper PHPDoc comments for type safety**
- **Use strict typing and proper return type declarations**
- **Follow PHPStan level 6 standards for static analysis**

### Testing

- **Update action tests to verify BaseAction inheritance**
- **Test both handle() and execute() methods for actions**
- **Verify logging functionality when using execute()**
- **Create component tests for reusable form components**
- **Test emit events and prop validation**

## PHPStan Static Analysis

PHPStan is configured to run at level 6, providing comprehensive static analysis of the PHP codebase. It helps catch:

- Type errors and inconsistencies
- Undefined variables and methods
- Dead code and unreachable statements
- Missing return types and PHPDoc annotations
- Laravel-specific issues through Larastan integration

### PHPStan Configuration

The `phpstan.neon` file includes:

- **Larastan extension** - Laravel-specific rules and understanding
- **Carbon extension** - Enhanced Carbon date library support
- **Analysis level 6** - Strict type checking without being overly restrictive
- **App directory scanning** - Focuses on application code

### Common PHPStan Issues and Solutions

```php
// ❌ DON'T: Missing return type
public function handle($data)
{
    return Task::create($data);
}

// ✅ DO: Proper return type declaration
public function handle(array $data): Task
{
    return Task::create($data);
}

// ❌ DON'T: Missing PHPDoc for complex types
public function transform($resource)
{
    return new TaskResource($resource);
}

// ✅ DO: Proper PHPDoc with generic types
/**
 * @param Task $resource
 * @return TaskResource
 */
public function transform(Task $resource): TaskResource
{
    return new TaskResource($resource);
}
```

## Automated Quality Checks with Husky

This project uses [Husky git hooks](https://typicode.github.io/husky/) to automatically enforce quality standards. See [`HUSKY_SETUP.md`](./HUSKY_SETUP.md) for complete documentation.

### Automatic Checks

**Pre-commit hooks** run minimal checks for fast commits:

- Minimal validation only

**Pre-push hooks** run comprehensive quality validation:

- PHPStan static analysis (`composer quality`)
- PHP tests (`php artisan test`)
- ESLint check (`npm run lint:check`)
- Prettier formatting check (`npm run format:check`)
- Frontend build (`npm run build`)
- Console.log detection (warns but doesn't block)

### Manual Quality Scripts

```bash
# Run all quality checks
npm run quality:all

# Run specific checks
npm run quality:backend    # PHPStan + tests
npm run quality:frontend   # ESLint + Prettier + build

# Auto-fix issues
npm run fix:frontend       # ESLint + Prettier auto-fix
```

## Validation Checklist

The following checks are **automatically enforced** by git hooks, but can also be run manually:

- [ ] All tests pass (`php artisan test`)
- [ ] PHPStan analysis passes (`composer quality`)
- [ ] Frontend builds without errors (`npm run build`)
- [ ] ESLint passes (`npm run lint:check`)
- [ ] Prettier formatting is correct (`npm run format:check`)
- [ ] No console errors in browser dev tools
- [ ] Semantic colors are used consistently
- [ ] Actions extend BaseAction
- [ ] Controllers use single-action pattern
- [ ] Form components follow reusable pattern
- [ ] Proper return types and PHPDoc comments are present

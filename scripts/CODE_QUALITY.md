# Code Quality Setup

This project uses ESLint and Prettier to maintain consistent code quality and formatting.

## Available Scripts

### Linting

- `npm run lint` - Run ESLint with auto-fix on the frontend code
- `npm run lint:check` - Check for linting issues without fixing them

### Formatting

- `npm run format` - Format all frontend code using Prettier
- `npm run format:check` - Check if code is properly formatted

## Configuration Files

- `eslint.config.js` - ESLint configuration using the flat config format
- `.prettierrc` - Prettier configuration for consistent formatting
- `.editorconfig` - Editor configuration for consistent settings across IDEs

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

### Testing

- **Update action tests to verify BaseAction inheritance**
- **Test both handle() and execute() methods for actions**
- **Verify logging functionality when using execute()**
- **Create component tests for reusable form components**
- **Test emit events and prop validation**

## Validation Checklist

Before committing code, ensure:

- [ ] All tests pass (`php artisan test`)
- [ ] Frontend builds without errors (`npm run build`)
- [ ] ESLint passes (`npm run lint:check`)
- [ ] Prettier formatting is correct (`npm run format:check`)
- [ ] No console errors in browser dev tools
- [ ] Semantic colors are used consistently
- [ ] Actions extend BaseAction
- [ ] Controllers use single-action pattern
- [ ] Form components follow reusable pattern

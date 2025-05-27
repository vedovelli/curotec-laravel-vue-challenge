# Curotec Laravel Vue Challenge

A modern full-stack task management application demonstrating proficiency in Laravel 12+, Vue 3, Inertia.js, and TailwindCSS. This project showcases clean architecture, SOLID principles, and modern development practices.

## 🚀 Project Overview

**Tech Stack:** Laravel 12.0+ | Vue 3 | Inertia.js | TailwindCSS | TypeScript  
**Status:** ✅ Core Implementation Complete - Refactored & Optimized  
**Test Coverage:** 149 tests passing with comprehensive coverage

### Features

- **Task Management System**: Full CRUD operations with advanced filtering
- **Real-time Dashboard**: Statistics and analytics with optimistic UI updates
- **Modern Architecture**: BaseAction pattern, reusable components, semantic design system
- **Quality Assurance**: Automated testing, linting, and pre-commit hooks

## 📋 Table of Contents

- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Development](#-development)
- [Testing](#-testing)
- [Code Quality](#-code-quality)
- [Architecture](#-architecture)
- [Application Routes](#-application-routes)
- [Deployment](#-deployment)
- [Contributing](#-contributing)

## ⚡ Quick Start

```bash
# Clone and setup
git clone <repository-url>
cd curotec-laravel-vue-challenge

# Backend setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Frontend setup
npm install
npm run dev

# Start development server
php artisan serve
```

Visit `http://localhost:8000` (Artisan serve) or `http://curotec-laravel-vue-challenge.test` (Valet/Herd) to see the application.

**Note**: This project uses PostgreSQL with Supabase. Make sure to configure your database credentials in the `.env` file before running migrations.

## 🛠 Installation

### Prerequisites

- **PHP 8.3+** with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Composer 2.0+**
- **Node.js 18+** and npm
- **PostgreSQL 13+** (using Supabase in this project)
- **Laravel Valet/Herd** (recommended for local development)

### Backend Setup

1. **Install Dependencies**

   ```bash
   composer install
   ```

2. **Environment Configuration**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Configuration**

   This project uses **PostgreSQL with Supabase**. Update your `.env` file with your database credentials:

   ```env
   APP_NAME=Laravel
   APP_ENV=local
   APP_KEY=base64:your-generated-key-here
   APP_DEBUG=true
   APP_URL=http://curotec-laravel-vue-challenge.test

   DB_CONNECTION=pgsql
   DB_HOST=your-supabase-host.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=your-supabase-password

   SESSION_DRIVER=database
   SESSION_LIFETIME=120

   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```

   **Note**: The project is configured to use:

   - **PostgreSQL** as the primary database
   - **Database-based sessions** for better scalability
   - **Database cache** for development simplicity
   - **Database queue** for background job processing

4. **Database Migration**
   ```bash
   php artisan migrate --seed
   ```

### Frontend Setup

1. **Install Dependencies**

   ```bash
   npm install
   ```

2. **Build Assets**

   ```bash
   # Development
   npm run dev

   # Production
   npm run build
   ```

### Local Development Environment

**Option 1: Laravel Valet/Herd (Recommended)**

```bash
# Install Valet globally
composer global require laravel/valet
valet install
valet park

# Or use Laravel Herd (GUI application)
# Download from https://herd.laravel.com
```

The project is configured to run at `http://curotec-laravel-vue-challenge.test` when using Valet/Herd.

**Option 2: Artisan Serve**

```bash
php artisan serve
```

This will serve the application at `http://localhost:8000`.

## 🔧 Development

### Development Workflow

1. **Start Development Servers**

   ```bash
   # Terminal 1: Backend
   php artisan serve

   # Terminal 2: Frontend (watch mode)
   npm run dev
   ```

2. **Database Operations**

   ```bash
   # Fresh migration with seeding
   php artisan migrate:fresh --seed

   # Create new migration
   php artisan make:migration create_example_table

   # Create new seeder
   php artisan make:seeder ExampleSeeder
   ```

3. **Code Generation**

   ```bash
   # Create new controller (single-action)
   php artisan make:controller ExampleController --invokable

   # Create new action
   php artisan make:action ExampleAction

   # Create new form request
   php artisan make:request ExampleRequest

   # Create new model with factory
   php artisan make:model Example -f
   ```

### Available Scripts

#### Backend Scripts

```bash
composer install          # Install PHP dependencies
composer update           # Update PHP dependencies
composer quality          # Run PHPStan static analysis
php artisan test          # Run PHP tests
php artisan test --coverage # Run tests with coverage
```

#### Frontend Scripts

```bash
npm install               # Install Node dependencies
npm run dev              # Start development server
npm run build            # Build for production
npm run preview          # Preview production build
npm run lint             # Run ESLint with auto-fix
npm run lint:check       # Check linting without fixing
npm run format           # Format code with Prettier
npm run format:check     # Check formatting
npm run test             # Run frontend tests
```

#### Quality Assurance Scripts

```bash
npm run quality:all      # Run all quality checks
npm run quality:backend  # Run backend quality checks
npm run quality:frontend # Run frontend quality checks
npm run fix:frontend     # Auto-fix frontend issues
```

## 🧪 Testing

### Running Tests

**Backend Tests (PHPUnit)**

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/TaskManagementTest.php

# Run tests with filter
php artisan test --filter=task
```

**Frontend Tests (Vitest)**

```bash
# Run all frontend tests
npm run test

# Run tests in watch mode
npm run test:watch

# Run tests with coverage
npm run test:coverage
```

### Test Structure

```
tests/
├── Feature/              # Integration tests
│   ├── TaskManagementTest.php
│   ├── DashboardTest.php
│   └── ApiTest.php
├── Unit/                 # Unit tests
│   ├── Actions/
│   ├── Models/
│   └── Requests/
└── TestCase.php         # Base test class
```

### Writing Tests

**Backend Test Example:**

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use Tests\TestCase;

final class TaskManagementTest extends TestCase
{
    public function test_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                ->assertJsonStructure(['id', 'title', 'description', 'status', 'due_date']);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }
}
```

## 📊 Code Quality

### Automated Quality Checks

This project uses **Husky** for automated quality enforcement:

- **Pre-commit**: Minimal validation for fast commits
- **Pre-push**: Comprehensive quality checks before sharing code

### Quality Tools

1. **PHPStan** (Level 6) - Static analysis for PHP
2. **ESLint** - JavaScript/TypeScript linting
3. **Prettier** - Code formatting
4. **PHP CS Fixer** - PHP code style fixing

### Configuration Files

- `phpstan.neon` - PHPStan configuration
- `eslint.config.js` - ESLint configuration (flat config)
- `.prettierrc` - Prettier configuration
- `.editorconfig` - Editor configuration

### Running Quality Checks

```bash
# Run all quality checks
npm run quality:all

# Individual checks
composer quality          # PHPStan
php artisan test         # PHP tests
npm run lint:check       # ESLint
npm run format:check     # Prettier
npm run build           # Frontend build

# Auto-fix issues
npm run lint            # Fix ESLint issues
npm run format          # Fix Prettier issues
```

### Bypassing Hooks (Emergency Only)

```bash
# Skip pre-commit hooks
git commit --no-verify -m "emergency fix"

# Skip pre-push hooks
git push --no-verify
```

⚠️ **Warning**: Only use `--no-verify` in genuine emergencies.

## 🏗 Architecture

### Backend Architecture

**Directory Structure:**

```
app/
├── Actions/              # Business logic (BaseAction pattern)
│   └── Task/
├── Http/
│   ├── Controllers/      # Single-action controllers
│   ├── Requests/         # Form request validation
│   ├── Resources/        # API resources
│   └── Traits/           # Reusable controller traits
├── Models/               # Eloquent models (final classes)
└── Services/             # Service layer
```

**Key Patterns:**

1. **BaseAction Pattern**

   ```php
   final class CreateTaskAction extends BaseAction
   {
       public function handle(array $data): Task
       {
           return Task::create($data);
       }
   }
   ```

2. **Single-Action Controllers**

   ```php
   final readonly class CreateTaskController
   {
       public function __invoke(CreateTaskRequest $request, CreateTaskAction $action): JsonResponse
       {
           $task = $action->handle($request->validated());
           return response()->json($task, 201);
       }
   }
   ```

3. **Form Request Validation**
   ```php
   final class CreateTaskRequest extends FormRequest
   {
       public function rules(): array
       {
           return [
               'title' => 'required|string|max:255',
               'description' => 'nullable|string|max:1000',
               'status' => 'required|in:pending,completed',
               'due_date' => 'nullable|date|after:today',
           ];
       }
   }
   ```

### Frontend Architecture

**Directory Structure:**

```
resources/js/
├── components/           # Reusable Vue components
│   ├── common/          # Shared components
│   ├── forms/           # Form components (TaskForm)
│   └── ui/              # UI library components
├── composables/         # Vue composables
├── layouts/             # Page layouts
├── pages/               # Page components
├── types/               # TypeScript definitions
└── utils/               # Utility functions
```

**Key Patterns:**

1. **Composition API with TypeScript**

   ```vue
   <script setup lang="ts">
   import { ref, computed } from 'vue';
   import type { Task } from '@/types';

   const tasks = ref<Task[]>([]);
   const pendingTasks = computed(() => tasks.value.filter((task) => task.status === 'pending'));
   </script>
   ```

2. **Inertia.js State Management**

   ```vue
   <script setup lang="ts">
   import { router } from '@inertiajs/vue3';

   // Props passed from Laravel controllers
   defineProps<{
     tasks: Task[];
     filters: FilterOptions;
   }>();

   // Navigate with state
   const handleFilter = (status: string) => {
     router.get('/tasks', { status }, { preserveState: true });
   };
   </script>
   ```

3. **Semantic Color System**
   ```vue
   <template>
     <div class="text-foreground bg-background border-border">
       <h1 class="text-foreground">Title</h1>
       <p class="text-muted-foreground">Description</p>
       <button class="text-primary hover:text-primary/80">Action</button>
     </div>
   </template>
   ```

### Database Schema

**Tasks Table:**

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
    due_date DATE NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    INDEX idx_created_at (created_at)
);
```

## 🌐 Application Routes

This application uses **Inertia.js** for seamless SPA-like navigation without traditional API endpoints. All routes return Inertia responses that render Vue components.

### Task Management Routes

**Web Routes (Inertia.js):**

#### Dashboard

```
GET /
```

**Controller:** `DashboardController`  
**Component:** `Dashboard.vue`  
**Description:** Main dashboard with task statistics and overview

#### Task List

```
GET /tasks
```

**Controller:** `ListTasksController`  
**Component:** `Tasks/Index.vue`  
**Description:** Paginated list of tasks with filtering options

#### Create Task

```
GET /tasks/create
POST /tasks
```

**Controllers:** `ShowCreateTaskController`, `CreateTaskController`  
**Component:** `Tasks/Create.vue`  
**Description:** Task creation form with validation

#### Edit Task

```
GET /tasks/{task}/edit
PUT /tasks/{task}
```

**Controllers:** `ShowEditTaskController`, `UpdateTaskController`  
**Component:** `Tasks/Edit.vue`  
**Description:** Task editing form with pre-populated data

#### Delete Task

```
DELETE /tasks/{task}
```

**Controller:** `DeleteTaskController`  
**Description:** Task deletion with confirmation

### Route Parameters & Responses

**Inertia Props Structure:**

```typescript
// Dashboard props
interface DashboardProps {
  stats: {
    total: number;
    pending: number;
    completed: number;
    completion_percentage: number;
  };
  recent_tasks: Task[];
}

// Task List props
interface TaskListProps {
  tasks: {
    data: Task[];
    meta: PaginationMeta;
  };
  filters: {
    status?: string;
    search?: string;
  };
}

// Task Form props
interface TaskFormProps {
  task?: Task; // Only present in edit mode
  errors?: ValidationErrors;
}
```

### Form Validation

All forms use **Laravel Form Requests** with real-time validation:

```typescript
// Task validation rules
interface TaskValidation {
  title: 'required|string|max:255';
  description: 'nullable|string|max:1000';
  status: 'required|in:pending,completed';
  due_date: 'nullable|date|after:today';
}
```

### Inertia.js Benefits

- **No API Endpoints**: Direct server-side rendering with client-side navigation
- **Shared State**: Props passed from Laravel controllers to Vue components
- **Form Handling**: Built-in form submission with validation error handling
- **Page Caching**: Automatic page caching for improved performance
- **SEO Friendly**: Server-side rendering with SPA user experience

## 🚀 Deployment

### Production Build

1. **Optimize Dependencies**

   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci --production
   ```

2. **Build Assets**

   ```bash
   npm run build
   ```

3. **Optimize Laravel**

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

### Environment Variables

**Required Production Variables:**

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (PostgreSQL/Supabase)
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Cache & Queue (Database-based for simplicity)
CACHE_STORE=database
QUEUE_CONNECTION=database

# Optional: Redis (if upgrading from database cache)
REDIS_CLIENT=phpredis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Broadcasting
BROADCAST_CONNECTION=log
```

### Server Requirements

- **PHP 8.3+** with required extensions (including `pgsql` for PostgreSQL)
- **Nginx** or **Apache** with proper configuration
- **PostgreSQL 13+** (Supabase or self-hosted)
- **Redis** (optional, for advanced caching and sessions)
- **SSL Certificate** for HTTPS

## 🤝 Contributing

### Development Guidelines

1. **Follow PSR-12** coding standards for PHP
2. **Use TypeScript** for all frontend code
3. **Write tests** for new features
4. **Follow semantic commit messages**
5. **Ensure quality checks pass** before pushing

### Commit Message Format

```
type(scope): description

feat(tasks): add task filtering functionality
fix(api): resolve validation error handling
docs(readme): update installation instructions
test(tasks): add unit tests for task creation
refactor(actions): implement BaseAction pattern
```

### Pull Request Process

1. **Create feature branch** from `main`
2. **Implement changes** with tests
3. **Run quality checks** locally
4. **Submit pull request** with description
5. **Address review feedback**
6. **Merge after approval**

### Code Review Checklist

- [ ] Code follows project conventions
- [ ] Tests are included and passing
- [ ] Documentation is updated
- [ ] No console.log statements in production code
- [ ] TypeScript types are properly defined
- [ ] Inertia.js props and components are properly structured

## 📚 Additional Resources

### Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Documentation](https://vuejs.org/guide/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)

### Project-Specific Guides

- [`scripts/PRD.md`](scripts/PRD.md) - Product Requirements Document
- [`scripts/CODE_QUALITY.md`](scripts/CODE_QUALITY.md) - Code Quality Guidelines
- [`scripts/QUICK_REFERENCE.md`](scripts/QUICK_REFERENCE.md) - Quick Reference Guide
- [`scripts/HUSKY_SETUP.md`](scripts/HUSKY_SETUP.md) - Git Hooks Configuration

### Task Management

- [`tasks/tasks.json`](tasks/tasks.json) - Project task definitions
- Individual task files in [`tasks/`](tasks/) directory

## 📄 License

This project is created for evaluation purposes as part of the Curotec technical challenge.

---

**Built with ❤️ using Laravel 12+, Vue 3, and modern web technologies.**

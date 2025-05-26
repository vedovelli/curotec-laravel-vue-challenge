# Quick Reference Guide

## Project Overview

**Tech Stack:** Laravel 12.0+ | Vue 3 | Inertia.js | Pinia | TailwindCSS

## Features Summary

### STORY-001: Task Management System

- **CRUD Operations**: Create, Read, Update, Delete tasks
- **Filtering**: All, Pending, Completed with reactive UI
- **Validation**: Real-time form validation with Laravel Form Requests
- **Fields**: title, description, status, due_date

### STORY-002: Real-time Dashboard

- **State Management**: Pinia stores for centralized state
- **Statistics**: Task counts, completion percentage, recent activity
- **Optimistic Updates**: Immediate UI feedback
- **Performance**: Efficient re-rendering and database queries

## Key Technical Requirements

### Backend (Laravel)

- Single Action Controllers as final, read-only classes
- Actions pattern with single `handle()` method
- Form Request Validation with strict typing (`declare(strict_types=1)`)
- API Resources for JSON transformation with type declarations
- Eloquent ORM with final models and proper relationships
- Database indexing for performance
- PSR-12 compliance and method injection over constructor injection

### Frontend (Vue 3)

- Composition API exclusively with TypeScript
- Pinia for state management with const functions
- Descriptive naming with "handle" prefix for event functions
- TailwindCSS exclusively (no CSS or `<style>` tags)
- Component composition with accessibility features (ARIA, tabindex, keyboard navigation)
- Early returns for improved code readability
- Const functions over regular functions with explicit types

### Database Schema

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

## Directory Structure

### Backend

```
app/
├── Actions/Task/
├── Http/Controllers/
├── Http/Requests/
├── Http/Resources/
├── Models/
└── Exceptions/
```

### Frontend

```
resources/js/
├── Components/
│   ├── Task/
│   ├── Dashboard/
│   └── UI/
├── Pages/
├── Stores/
└── Types/
```

## Validation Rules

- **Title**: Required, string, max 255 characters
- **Description**: Optional, string, max 1000 characters
- **Status**: Required, in ['pending', 'completed']
- **Due Date**: Optional, date, not in past for new tasks

## Color Scheme

- Primary: `#3B82F6` (Blue-500)
- Success: `#10B981` (Emerald-500)
- Warning: `#F59E0B` (Amber-500)
- Error: `#EF4444` (Red-500)
- Neutral: `#6B7280` (Gray-500)

## Performance Targets

- API Response Time: < 200ms
- First Contentful Paint: < 1.5s
- Database Query Time: < 50ms
- Test Coverage: 90%+ backend, 80%+ frontend

## Key Acceptance Criteria Checklist

### Task Management

- [ ] CRUD operations work correctly
- [ ] Real-time form validation
- [ ] Filter by status (All, Pending, Completed)
- [ ] Responsive design
- [ ] Confirmation dialogs for deletion

### Dashboard

- [ ] Statistics cards with real-time data
- [ ] Pinia store manages all state
- [ ] Optimistic UI updates
- [ ] Efficient component re-rendering
- [ ] Quick actions panel

## Development Commands

```bash
# Backend
composer install
php artisan migrate
php artisan serve

# Frontend
npm install
npm run dev

# Testing
php artisan test
npm run test
```

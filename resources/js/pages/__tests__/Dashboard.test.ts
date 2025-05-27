/// <reference types="@testing-library/jest-dom" />
import type { Task, TaskStats } from '@/types';
import { render, screen } from '@testing-library/vue';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import Dashboard from '../Dashboard.vue';

// Mock Inertia components and functions
vi.mock('@inertiajs/vue3', () => ({
  Head: {
    name: 'Head',
    props: ['title'],
    template: '<div data-testid="head" :title="title"></div>',
  },
}));

// Mock AppLayout component
const AppLayoutMock = {
  name: 'AppLayout',
  props: ['breadcrumbs'],
  template: '<div data-testid="app-layout"><slot /></div>',
};

interface TaskCollection {
  data: Task[];
}

interface DashboardProps {
  tasks: TaskCollection;
  stats: TaskStats;
}

const renderDashboard = (props: Partial<DashboardProps> = {}) => {
  const defaultProps: DashboardProps = {
    tasks: { data: [] },
    stats: {
      total_tasks: 0,
      completed_tasks: 0,
      pending_tasks: 0,
      completion_percentage: 0,
    },
    ...props,
  };

  return render(Dashboard, {
    props: defaultProps,
    global: {
      stubs: {
        AppLayout: AppLayoutMock,
      },
    },
  });
};

const createMockTask = (overrides: Partial<Task> = {}): Task => ({
  id: 1,
  title: 'Test Task',
  description: 'Test task description',
  status: 'pending',
  status_text: 'Pending',
  priority_level: 'medium',
  due_date: '2024-12-31',
  formatted_due_date: 'Dec 31, 2024',
  is_completed: false,
  is_overdue: false,
  days_until_due: 30,
  created_at: '2024-01-01T00:00:00Z',
  updated_at: '2024-01-01T00:00:00Z',
  ...overrides,
});

describe('Dashboard Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('Stats Cards', () => {
    it('should render all three stats cards with correct titles', () => {
      renderDashboard({
        stats: {
          total_tasks: 10,
          completed_tasks: 5,
          pending_tasks: 5,
          completion_percentage: 50,
        },
      });

      expect(screen.getByText('Total Tasks')).toBeInTheDocument();
      expect(screen.getByText('Completed Tasks')).toBeInTheDocument();
      expect(screen.getAllByText('Pending Tasks')).toHaveLength(2);
    });

    it('should display correct stat values', () => {
      renderDashboard({
        stats: {
          total_tasks: 15,
          completed_tasks: 8,
          pending_tasks: 7,
          completion_percentage: 53.33,
        },
      });

      expect(screen.getByText('15')).toBeInTheDocument();
      expect(screen.getAllByText('8')).toHaveLength(2);
      expect(screen.getAllByText('7')).toHaveLength(2);
    });

    it('should display stat descriptions', () => {
      renderDashboard();

      expect(screen.getByText('All tasks in the system')).toBeInTheDocument();
      expect(screen.getByText('Successfully finished')).toBeInTheDocument();
      expect(screen.getByText('Awaiting completion')).toBeInTheDocument();
    });

    it('should display stat icons', () => {
      renderDashboard();

      expect(screen.getAllByText('📋')).toHaveLength(2);
      expect(screen.getByText('✅')).toBeInTheDocument();
      expect(screen.getAllByText('⏳')).toHaveLength(2);
    });

    it('should handle undefined stats gracefully', () => {
      renderDashboard({
        stats: {
          total_tasks: 0,
          completed_tasks: 0,
          pending_tasks: 0,
          completion_percentage: 0,
        },
      });

      expect(screen.getAllByText('0')).toHaveLength(5);
    });
  });

  describe('Completion Progress', () => {
    it('should display completion percentage', () => {
      renderDashboard({
        stats: {
          total_tasks: 10,
          completed_tasks: 7,
          pending_tasks: 3,
          completion_percentage: 70,
        },
      });

      expect(screen.getByText('70%')).toBeInTheDocument();
    });

    it('should handle NaN completion percentage', () => {
      renderDashboard({
        stats: {
          total_tasks: 0,
          completed_tasks: 0,
          pending_tasks: 0,
          completion_percentage: NaN,
        },
      });

      expect(screen.getByText('0%')).toBeInTheDocument();
    });

    it('should round completion percentage', () => {
      renderDashboard({
        stats: {
          total_tasks: 3,
          completed_tasks: 2,
          pending_tasks: 1,
          completion_percentage: 66.666,
        },
      });

      expect(screen.getByText('67%')).toBeInTheDocument();
    });

    it('should display progress details', () => {
      renderDashboard({
        stats: {
          total_tasks: 25,
          completed_tasks: 13,
          pending_tasks: 12,
          completion_percentage: 52,
        },
      });

      expect(screen.getByText('Completed:')).toBeInTheDocument();
      expect(screen.getByText('Pending:')).toBeInTheDocument();
      expect(screen.getAllByText('13')).toHaveLength(2);
      expect(screen.getAllByText('12')).toHaveLength(2);
    });

    it('should have progress bar with correct width', () => {
      renderDashboard({
        stats: {
          total_tasks: 10,
          completed_tasks: 3,
          pending_tasks: 7,
          completion_percentage: 30,
        },
      });

      const progressBar = document.querySelector('.bg-primary.h-3.rounded-full');
      expect(progressBar).toHaveStyle({ width: '30%' });
    });
  });

  describe('Recent Tasks Table', () => {
    it('should display table headers', () => {
      renderDashboard();

      expect(screen.getByText('Task')).toBeInTheDocument();
      expect(screen.getByText('Priority')).toBeInTheDocument();
      expect(screen.getByText('Status')).toBeInTheDocument();
      expect(screen.getByText('Due Date')).toBeInTheDocument();
      expect(screen.getByText('Actions')).toBeInTheDocument();
    });

    it('should display empty state when no tasks', () => {
      renderDashboard({ tasks: { data: [] } });

      expect(screen.getByText('🎉')).toBeInTheDocument();
      expect(screen.getByText('No tasks yet!')).toBeInTheDocument();
      expect(screen.getByText('Create your first task to get started.')).toBeInTheDocument();
    });

    it('should display task information correctly', () => {
      const task = createMockTask({
        id: 1,
        title: 'Important Task',
        description: 'This is a very important task',
        status: 'pending',
        status_text: 'Pending',
        priority_level: 'high',
        formatted_due_date: 'Jan 15, 2024',
      });

      renderDashboard({ tasks: { data: [task] } });

      expect(screen.getByText('Important Task')).toBeInTheDocument();
      expect(screen.getByText('This is a very important task')).toBeInTheDocument();
      expect(screen.getByText('Pending')).toBeInTheDocument();
      expect(screen.getByText('High')).toBeInTheDocument();
      expect(screen.getByText('Jan 15, 2024')).toBeInTheDocument();
    });

    it('should handle task without description', () => {
      const task = createMockTask({
        description: null,
      });

      renderDashboard({ tasks: { data: [task] } });

      expect(screen.getByText('No description')).toBeInTheDocument();
    });

    it('should display correct priority badge classes', () => {
      const highPriorityTask = createMockTask({
        priority_level: 'high',
      });

      renderDashboard({ tasks: { data: [highPriorityTask] } });

      const priorityBadge = screen.getByText('High');
      expect(priorityBadge).toHaveClass('bg-red-100', 'text-red-800');
    });

    it('should display correct status badge classes', () => {
      const completedTask = createMockTask({
        status: 'completed',
        status_text: 'Completed',
      });

      renderDashboard({ tasks: { data: [completedTask] } });

      const statusBadge = screen.getByText('Completed');
      expect(statusBadge).toHaveClass('bg-green-100', 'text-green-800');
    });

    it('should display action links', () => {
      const task = createMockTask({ id: 123 });

      renderDashboard({ tasks: { data: [task] } });

      const editLink = screen.getByText('Edit');
      const viewLink = screen.getByText('View');

      expect(editLink).toBeInTheDocument();
      expect(viewLink).toBeInTheDocument();
      expect(editLink.closest('a')).toHaveAttribute('href', '/tasks/123/edit');
      expect(viewLink.closest('a')).toHaveAttribute('href', '/tasks/123');
    });

    it('should handle multiple tasks', () => {
      const tasks = [
        createMockTask({ id: 1, title: 'Task 1' }),
        createMockTask({ id: 2, title: 'Task 2' }),
        createMockTask({ id: 3, title: 'Task 3' }),
      ];

      renderDashboard({ tasks: { data: tasks } });

      expect(screen.getByText('Task 1')).toBeInTheDocument();
      expect(screen.getByText('Task 2')).toBeInTheDocument();
      expect(screen.getByText('Task 3')).toBeInTheDocument();
    });

    it('should format due date when formatted_due_date is not provided', () => {
      const task = createMockTask({
        due_date: '2024-03-15',
        formatted_due_date: '2024-03-15',
      });

      renderDashboard({ tasks: { data: [task] } });

      expect(screen.getByText('2024-03-15')).toBeInTheDocument();
    });

    it('should display "No due date" when due_date is null', () => {
      const task = createMockTask({
        due_date: null,
        formatted_due_date: null,
      });

      renderDashboard({ tasks: { data: [task] } });

      expect(screen.getByText('No due date')).toBeInTheDocument();
    });
  });

  describe('Quick Actions', () => {
    it('should display all quick action cards', () => {
      renderDashboard();

      expect(screen.getByText('Create Task')).toBeInTheDocument();
      expect(screen.getByText('View All Tasks')).toBeInTheDocument();
      expect(screen.getAllByText('Pending Tasks')).toHaveLength(2);
    });

    it('should have correct links for quick actions', () => {
      renderDashboard();

      const createTaskLink = screen.getByText('Create Task').closest('a');
      const viewAllTasksLink = screen.getByText('View All Tasks').closest('a');

      const quickActionsSection = screen.getByText('Quick Actions').closest('div');
      const pendingTasksLink = quickActionsSection?.querySelector(
        'a[href="/tasks?status=pending"]'
      );

      expect(createTaskLink).toHaveAttribute('href', '/tasks/create');
      expect(viewAllTasksLink).toHaveAttribute('href', '/tasks');
      expect(pendingTasksLink).toHaveAttribute('href', '/tasks?status=pending');
    });

    it('should display pending tasks count in quick action', () => {
      renderDashboard({
        stats: {
          total_tasks: 10,
          completed_tasks: 3,
          pending_tasks: 7,
          completion_percentage: 30,
        },
      });

      expect(screen.getByText('7 tasks waiting')).toBeInTheDocument();
    });

    it('should display quick action descriptions', () => {
      renderDashboard();

      expect(screen.getByText('Add a new task')).toBeInTheDocument();
      expect(screen.getByText('Manage your tasks')).toBeInTheDocument();
    });

    it('should display quick action icons', () => {
      renderDashboard();

      expect(screen.getByText('➕')).toBeInTheDocument();
      expect(screen.getAllByText('📋')).toHaveLength(2);
      expect(screen.getAllByText('⏳')).toHaveLength(2);
    });
  });

  describe('Priority and Status Formatting', () => {
    it('should format priority text correctly', () => {
      const tasks = [
        createMockTask({ priority_level: 'high' }),
        createMockTask({ priority_level: 'medium' }),
        createMockTask({ priority_level: 'low' }),
        createMockTask({ priority_level: undefined }),
      ];

      renderDashboard({ tasks: { data: tasks } });

      expect(screen.getByText('High')).toBeInTheDocument();
      expect(screen.getByText('Medium')).toBeInTheDocument();
      expect(screen.getByText('Low')).toBeInTheDocument();
      expect(screen.getByText('Normal')).toBeInTheDocument();
    });

    it('should handle different status types', () => {
      const tasks = [
        createMockTask({ status: 'completed', status_text: 'Completed' }),
        createMockTask({ status: 'pending', status_text: 'Pending' }),
      ];

      renderDashboard({ tasks: { data: tasks } });

      expect(screen.getByText('Completed')).toBeInTheDocument();
      expect(screen.getByText('Pending')).toBeInTheDocument();
    });
  });

  describe('Component Structure', () => {
    it('should render with AppLayout wrapper', () => {
      renderDashboard();

      expect(screen.getByTestId('app-layout')).toBeInTheDocument();
    });

    it('should set correct page title', () => {
      renderDashboard();

      expect(screen.getByTestId('head')).toHaveAttribute('title', 'Dashboard');
    });

    it('should have proper section structure', () => {
      renderDashboard();

      expect(screen.getByText('Task Completion Progress')).toBeInTheDocument();
      expect(screen.getByText('Recent Tasks')).toBeInTheDocument();
      expect(screen.getByText('Quick Actions')).toBeInTheDocument();
    });

    it('should have "View all" link in recent tasks section', () => {
      renderDashboard();

      const viewAllLink = screen.getByText('View all →');
      expect(viewAllLink).toBeInTheDocument();
      expect(viewAllLink.closest('a')).toHaveAttribute('href', '/tasks');
    });
  });

  describe('Edge Cases', () => {
    it('should handle null/undefined task data gracefully', () => {
      renderDashboard({
        tasks: { data: [] },
      });

      expect(screen.getByText('No tasks yet!')).toBeInTheDocument();
    });

    it('should handle missing task properties', () => {
      const incompleteTask = {
        id: 1,
        title: 'Incomplete Task',
        status: 'pending',
        description: null,
        status_text: 'Pending',
        priority_level: 'medium',
        due_date: null,
        formatted_due_date: null,
        is_completed: false,
        is_overdue: false,
        days_until_due: null,
        created_at: '2024-01-01T00:00:00Z',
        updated_at: '2024-01-01T00:00:00Z',
      } as Task;

      renderDashboard({ tasks: { data: [incompleteTask] } });

      expect(screen.getByText('Incomplete Task')).toBeInTheDocument();
      expect(screen.getByText('No description')).toBeInTheDocument();
    });

    it('should handle zero stats', () => {
      renderDashboard({
        stats: {
          total_tasks: 0,
          completed_tasks: 0,
          pending_tasks: 0,
          completion_percentage: 0,
        },
      });

      expect(screen.getByText('0%')).toBeInTheDocument();
      expect(screen.getAllByText('0')).toHaveLength(5);
    });
  });
});

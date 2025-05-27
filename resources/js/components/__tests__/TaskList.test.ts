/// <reference types="@testing-library/jest-dom" />
import type { PaginatedTasks, Task, TaskStats, TaskStatus } from '@/types';
import { render, screen } from '@testing-library/vue';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import TaskList from '../TaskList.vue';

// Mock Inertia router
const mockRouter = {
  visit: vi.fn(),
  on: vi.fn(),
};

const createMockTask = (overrides: Partial<Task> = {}): Task => ({
  id: 1,
  title: 'Test Task',
  description: 'Test Description',
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

const createMockPaginatedTasks = (tasks: Task[] = [], overrides = {}): PaginatedTasks => ({
  data: tasks,
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: tasks.length,
  from: tasks.length > 0 ? 1 : null,
  to: tasks.length,
  links: [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '/tasks?page=1', label: '1', active: true },
    { url: null, label: 'Next &raquo;', active: false },
  ],
  ...overrides,
});

const createMockTaskStats = (overrides = {}): TaskStats => ({
  total_tasks: 5,
  pending_tasks: 3,
  completed_tasks: 2,
  ...overrides,
});

const renderTaskList = (props = {}) => {
  const defaultProps = {
    tasks: createMockPaginatedTasks([createMockTask()]),
    taskStats: createMockTaskStats(),
    currentFilter: 'all' as TaskStatus,
    ...props,
  };

  return render(TaskList, {
    props: defaultProps,
    global: {
      mocks: {
        router: mockRouter,
        route: (name: string, params?: string | number) => `/tasks/${params || ''}`,
      },
      stubs: {
        TaskFilters: {
          template: '<div data-testid="task-filters">Task Filters</div>',
        },
        TaskCard: {
          template: '<div data-testid="task-card">{{ task.title }}</div>',
          props: ['task'],
        },
      },
    },
  });
};

describe('TaskList Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render task filters', () => {
    renderTaskList();

    expect(screen.getByTestId('task-filters')).toBeInTheDocument();
  });

  it('should render task cards when tasks are available', () => {
    const tasks = [
      createMockTask({ id: 1, title: 'Task 1' }),
      createMockTask({ id: 2, title: 'Task 2' }),
    ];

    renderTaskList({
      tasks: createMockPaginatedTasks(tasks),
    });

    expect(screen.getByText('Task 1')).toBeInTheDocument();
    expect(screen.getByText('Task 2')).toBeInTheDocument();
    expect(screen.getAllByTestId('task-card')).toHaveLength(2);
  });

  it('should render empty state when no tasks are available', () => {
    renderTaskList({
      tasks: createMockPaginatedTasks([]),
    });

    expect(screen.getByText('No tasks found')).toBeInTheDocument();
    expect(screen.getByText(/try adjusting your filters/i)).toBeInTheDocument();
  });

  it('should render tasks in a grid layout', () => {
    const tasks = [
      createMockTask({ id: 1, title: 'Task 1' }),
      createMockTask({ id: 2, title: 'Task 2' }),
    ];

    const { container } = renderTaskList({
      tasks: createMockPaginatedTasks(tasks),
    });

    const taskGrid = container.querySelector('[role="list"]');
    expect(taskGrid).toHaveClass(
      'grid',
      'gap-6',
      'grid-cols-1',
      'md:grid-cols-2',
      'lg:grid-cols-3',
      'xl:grid-cols-4'
    );
  });

  it('should render pagination when multiple pages exist', () => {
    const paginatedTasks = createMockPaginatedTasks([createMockTask()], {
      current_page: 1,
      last_page: 3,
      links: [
        { url: null, label: '&laquo; Previous', active: false },
        { url: '/tasks?page=1', label: '1', active: true },
        { url: '/tasks?page=2', label: '2', active: false },
        { url: '/tasks?page=3', label: '3', active: false },
        { url: '/tasks?page=2', label: 'Next &raquo;', active: false },
      ],
    });

    renderTaskList({ tasks: paginatedTasks });

    // Check for pagination buttons using aria-labels
    expect(screen.getByRole('button', { name: /current page, page 1/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /go to page 2/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /go to page 3/i })).toBeInTheDocument();
  });

  it('should not render pagination when only one page exists', () => {
    const paginatedTasks = createMockPaginatedTasks([createMockTask()], {
      current_page: 1,
      last_page: 1,
    });

    renderTaskList({ tasks: paginatedTasks });

    // Pagination should not be rendered when there's only one page
    expect(screen.queryByLabelText('Pagination')).not.toBeInTheDocument();
  });

  it('should highlight active pagination page', () => {
    const paginatedTasks = createMockPaginatedTasks([createMockTask()], {
      current_page: 2,
      last_page: 3,
      links: [
        { url: '/tasks?page=1', label: '&laquo; Previous', active: false },
        { url: '/tasks?page=1', label: '1', active: false },
        { url: '/tasks?page=2', label: '2', active: true },
        { url: '/tasks?page=3', label: '3', active: false },
        { url: '/tasks?page=3', label: 'Next &raquo;', active: false },
      ],
    });

    renderTaskList({ tasks: paginatedTasks });

    const activePage = screen.getByRole('button', { name: /current page, page 2/i });
    expect(activePage).toHaveClass('bg-primary', 'text-primary-foreground');
  });

  it('should show pagination info when tasks are present', () => {
    const paginatedTasks = createMockPaginatedTasks([createMockTask()], {
      from: 1,
      to: 5,
      total: 15,
      last_page: 3,
    });

    renderTaskList({ tasks: paginatedTasks });

    // Check for pagination info text parts in the desktop pagination section
    const paginationInfo = screen.getByText((content, element) => {
      return (
        element?.tagName.toLowerCase() === 'p' &&
        element?.className.includes('text-foreground') &&
        content.includes('Showing')
      );
    });
    expect(paginationInfo).toBeInTheDocument();
  });

  it('should have proper accessibility attributes for task list', () => {
    const tasks = [createMockTask({ title: 'Accessible Task' })];

    renderTaskList({
      tasks: createMockPaginatedTasks(tasks),
    });

    const taskList = screen.getByRole('list');
    expect(taskList).toHaveAttribute('aria-label', 'Task list');
  });

  it('should apply custom class when provided', () => {
    const { container } = renderTaskList({ class: 'custom-class' });

    expect(container.firstChild).toHaveClass('custom-class');
  });

  it('should handle empty state with different filter messages', () => {
    renderTaskList({
      tasks: createMockPaginatedTasks([]),
      currentFilter: 'pending',
    });

    expect(screen.getByText('No tasks found')).toBeInTheDocument();
    expect(screen.getByText(/try adjusting your filters/i)).toBeInTheDocument();
  });

  it('should render component without errors', () => {
    // This test verifies the component renders without errors
    const { container } = renderTaskList();
    expect(container.firstChild).toBeInTheDocument();
  });

  it('should show screen reader status updates', () => {
    renderTaskList();

    // Check for screen reader status element - look for the parent div with aria attributes
    const statusContainer = screen.getByText(/showing all.*tasks/i).closest('div');
    expect(statusContainer).toHaveAttribute('aria-live', 'polite');
    expect(statusContainer).toHaveAttribute('aria-atomic', 'true');
  });
});

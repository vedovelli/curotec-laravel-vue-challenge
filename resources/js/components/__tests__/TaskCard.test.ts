import { render, screen } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';

/// <reference types="@testing-library/jest-dom" />
import type { Task } from '@/types';
import TaskCard from '../TaskCard.vue';

// Mock Inertia's Link component and route helper
const mockRoute = (name: string, params?: string | number) => `/tasks/${params}`;

const renderTaskCard = (task: Partial<Task> = {}) => {
  const defaultTask: Task = {
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
    ...task,
  };

  return render(TaskCard, {
    props: { task: defaultTask },
    global: {
      mocks: {
        route: mockRoute,
      },
      stubs: {
        Link: {
          template: '<a :href="href" v-bind="$attrs"><slot /></a>',
          props: ['href'],
        },
      },
    },
  });
};

describe('TaskCard Component', () => {
  it('should render task title and description', () => {
    renderTaskCard({
      title: 'Sample Task Title',
      description: 'Sample task description',
    });

    expect(screen.getByText('Sample Task Title')).toBeInTheDocument();
    expect(screen.getByText('Sample task description')).toBeInTheDocument();
  });

  it('should display correct status badge', () => {
    renderTaskCard({ status: 'completed' });

    expect(screen.getByText('Completed')).toBeInTheDocument();
  });

  it('should display correct priority badge', () => {
    renderTaskCard({ priority_level: 'high' });

    expect(screen.getByText('high')).toBeInTheDocument();
  });

  it('should show due date when provided', () => {
    renderTaskCard({ due_date: '2024-12-31' });

    // Look specifically for the due date in the clock icon section
    const dueDateSection = screen.getByText('Dec 30, 2024');
    expect(dueDateSection).toBeInTheDocument();
  });

  it('should not show due date section when due_date is null', () => {
    renderTaskCard({ due_date: null });

    expect(screen.queryByText(/Due/)).not.toBeInTheDocument();
  });

  it('should render without description when not provided', () => {
    renderTaskCard({ description: null });

    expect(screen.getByText('Test Task')).toBeInTheDocument();
    expect(screen.queryByText('Test task description')).not.toBeInTheDocument();
  });

  it('should apply correct status color classes', () => {
    renderTaskCard({ status: 'completed' });

    const statusBadge = screen.getByText('Completed');
    expect(statusBadge).toHaveClass('bg-green-50', 'text-green-700');
  });

  it('should apply correct priority color classes', () => {
    renderTaskCard({ priority_level: 'high' });

    const priorityBadge = screen.getByText('high');
    expect(priorityBadge).toHaveClass('bg-red-50', 'text-red-700');
  });

  it('should have proper accessibility attributes', () => {
    renderTaskCard({ title: 'Accessible Task' });

    const link = screen.getByRole('link');
    expect(link).toHaveAttribute('aria-label', 'View task: Accessible Task');
  });

  it('should render assignee initials', () => {
    renderTaskCard();

    expect(screen.getByText('FV')).toBeInTheDocument();
  });

  it('should have hover effects on the card', () => {
    renderTaskCard();

    const cardLink = screen.getByRole('link');
    expect(cardLink).toHaveClass('hover:border-gray-300', 'hover:shadow-lg');
  });
});

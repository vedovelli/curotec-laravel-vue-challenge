import { describe, expect, it } from 'vitest';
/// <reference types="@testing-library/jest-dom" />
import { render, screen } from '@testing-library/vue';

import TaskForm from '../TaskForm.vue';

// Mock Inertia's Link component
const mockRoute = (name: string, params?: string | number) => `/tasks/${params || ''}`;

const renderTaskForm = (props = {}) => {
  const defaultProps = {
    form: {
      title: '',
      description: '',
      status: '',
      priority: 'medium',
      due_date: '',
      assignee: '',
      errors: {},
      processing: false,
    },
    mode: 'create' as const,
    cancelRoute: '/tasks',
    submitText: 'Create Task',
    processingText: 'Creating...',
    ...props,
  };

  return render(TaskForm, {
    props: defaultProps,
    global: {
      mocks: {
        route: mockRoute,
      },
      stubs: {
        Link: {
          template: '<a :href="href" v-bind="$attrs"><slot /></a>',
          props: ['href'],
        },
        InputError: {
          template: '<div v-if="message" class="error">{{ message }}</div>',
          props: ['message'],
        },
      },
    },
  });
};

describe('TaskForm Component', () => {
  it('should render form fields correctly', () => {
    renderTaskForm();

    expect(screen.getByLabelText(/task title/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/description/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/status/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/priority/i)).toBeInTheDocument();

    // Check for the Due Date label specifically
    const dueDateLabel = screen.getByText((content, element) => {
      return element?.tagName.toLowerCase() === 'label' && content === 'Due Date';
    });
    expect(dueDateLabel).toBeInTheDocument();
  });

  it('should display correct title for create mode', () => {
    renderTaskForm({ mode: 'create' });

    expect(screen.getByText('Create New Task')).toBeInTheDocument();
  });

  it('should display correct title for edit mode', () => {
    renderTaskForm({ mode: 'edit' });

    expect(screen.getByText('Edit Task')).toBeInTheDocument();
  });

  it('should populate form fields with initial values', () => {
    const form = {
      title: 'Test Task',
      description: 'Test Description',
      status: 'pending',
      priority: 'high',
      due_date: '2024-12-31',
      assignee: 'John Doe',
      errors: {},
      processing: false,
    };

    renderTaskForm({ form });

    expect(screen.getByDisplayValue('Test Task')).toBeInTheDocument();
    expect(screen.getByDisplayValue('Test Description')).toBeInTheDocument();
    expect(screen.getByDisplayValue('John Doe')).toBeInTheDocument();
  });

  it('should show validation errors', () => {
    const form = {
      title: '',
      description: '',
      status: '',
      priority: 'medium',
      due_date: '',
      assignee: '',
      errors: {
        title: 'Title is required',
        status: 'Status is required',
      },
      processing: false,
    };

    renderTaskForm({ form });

    expect(screen.getByText('Title is required')).toBeInTheDocument();
    expect(screen.getByText('Status is required')).toBeInTheDocument();
  });

  it('should show processing state when form is submitting', () => {
    const form = {
      title: 'Test Task',
      description: '',
      status: 'pending',
      priority: 'medium',
      due_date: '',
      assignee: '',
      errors: {},
      processing: true,
    };

    renderTaskForm({ form, processingText: 'Creating...' });

    const submitButton = screen.getByRole('button', { name: /creating/i });
    expect(submitButton).toBeDisabled();
    expect(screen.getByText('Creating...')).toBeInTheDocument();
  });

  it('should have required field indicators', () => {
    renderTaskForm();

    // Check for required asterisks
    expect(screen.getByText('Task Title')).toBeInTheDocument();
    expect(screen.getByText('Status')).toBeInTheDocument();

    // Both should have required asterisks in their labels
    const titleLabel = screen.getByText('Task Title').closest('label');
    const statusLabel = screen.getByText('Status').closest('label');

    expect(titleLabel).toHaveTextContent('*');
    expect(statusLabel).toHaveTextContent('*');
  });

  it('should have cancel link with correct href', () => {
    renderTaskForm({ cancelRoute: '/tasks/1' });

    const cancelLink = screen.getByRole('link', { name: /cancel/i });
    expect(cancelLink).toHaveAttribute('href', '/tasks/1');
  });

  it('should handle status options correctly', () => {
    renderTaskForm();

    const statusSelect = screen.getByLabelText(/status/i);
    const options = Array.from(statusSelect.querySelectorAll('option'));

    expect(options).toHaveLength(4); // Including the default "Select status" option
    expect(options.map((option) => option.textContent)).toEqual([
      'Select status',
      'Pending',
      'In Progress',
      'Completed',
    ]);
  });

  it('should handle priority options correctly', () => {
    renderTaskForm();

    const prioritySelect = screen.getByLabelText(/priority/i);
    const options = Array.from(prioritySelect.querySelectorAll('option'));

    expect(options).toHaveLength(3);
    expect(options.map((option) => option.textContent)).toEqual(['Low', 'Medium', 'High']);
  });

  it('should render submit button with correct text', () => {
    renderTaskForm({ submitText: 'Update Task' });

    expect(screen.getByRole('button', { name: /update task/i })).toBeInTheDocument();
  });
});

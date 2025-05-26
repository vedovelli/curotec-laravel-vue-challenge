/**
 * Format a date string for display in task components
 */
export const formatTaskDate = (dateString: string | null): string => {
  if (!dateString) return '';

  const date = new Date(dateString);
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const taskDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

  const diffTime = taskDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  // Format options for different scenarios
  const shortFormat = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
  });

  const fullFormat = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });

  // Return relative or absolute date based on proximity
  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Tomorrow';
  if (diffDays === -1) return 'Yesterday';
  if (diffDays > 1 && diffDays <= 7) return `In ${diffDays} days`;
  if (diffDays < -1 && diffDays >= -7) return `${Math.abs(diffDays)} days ago`;

  // For dates further away, show the actual date
  const currentYear = now.getFullYear();
  const taskYear = date.getFullYear();

  return taskYear === currentYear ? shortFormat.format(date) : fullFormat.format(date);
};

/**
 * Get the relative time description for a task due date
 */
export const getRelativeTimeText = (dateString: string | null, isCompleted: boolean): string => {
  if (!dateString) return '';
  if (isCompleted) return '';

  const date = new Date(dateString);
  const now = new Date();
  const diffTime = date.getTime() - now.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 0) return 'Overdue';
  if (diffDays === 0) return 'Due today';
  if (diffDays === 1) return 'Due tomorrow';
  if (diffDays <= 7) return `Due in ${diffDays} days`;

  return '';
};

/**
 * Check if a date is overdue
 */
export const isOverdue = (dateString: string | null, isCompleted: boolean): boolean => {
  if (!dateString || isCompleted) return false;

  const date = new Date(dateString);
  const now = new Date();

  return date < now;
};

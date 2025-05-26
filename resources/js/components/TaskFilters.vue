<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { TaskStatus } from '@/types';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Filter, List } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Props {
  currentFilter?: TaskStatus;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentFilter: 'all',
});

// Reactive state for the current filter
const activeFilter = ref<TaskStatus>(props.currentFilter);

// Filter options configuration
const filterOptions = [
  {
    value: 'all' as TaskStatus,
    label: 'All Tasks',
    icon: List,
    description: 'Show all tasks',
  },
  {
    value: 'pending' as TaskStatus,
    label: 'Pending',
    icon: Clock,
    description: 'Show pending tasks',
  },
  {
    value: 'completed' as TaskStatus,
    label: 'Completed',
    icon: CheckCircle2,
    description: 'Show completed tasks',
  },
];

// Read initial filter state from URL on component mount
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const statusParam = urlParams.get('status') as TaskStatus;

  if (statusParam && ['all', 'pending', 'completed'].includes(statusParam)) {
    activeFilter.value = statusParam;
  } else {
    activeFilter.value = 'all';
  }
});

// Handle filter change
const handleFilterChange = (newFilter: TaskStatus) => {
  activeFilter.value = newFilter;

  // Update URL using Inertia.js
  const currentUrl = new URL(window.location.href);
  const searchParams = new URLSearchParams(currentUrl.search);

  if (newFilter === 'all') {
    searchParams.delete('status');
  } else {
    searchParams.set('status', newFilter);
  }

  // Reset to first page when changing filters
  searchParams.delete('page');

  const newUrl = `${currentUrl.pathname}${searchParams.toString() ? '?' + searchParams.toString() : ''}`;

  router.visit(newUrl, {
    preserveState: true,
    preserveScroll: true,
    only: ['tasks'], // Only reload the tasks data
  });
};

// Computed classes for filter buttons
const getButtonClasses = (filterValue: TaskStatus) => {
  const isActive = activeFilter.value === filterValue;

  return cn(
    'flex items-center gap-2 px-3 py-2 text-sm font-medium transition-all duration-200',
    'border border-gray-200 dark:border-gray-700',
    'focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900',
    isActive
      ? 'border-blue-600 bg-blue-600 text-white shadow-sm hover:bg-blue-700'
      : 'bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700'
  );
};

// Handle keyboard navigation
const handleKeydown = (event: KeyboardEvent, filterValue: TaskStatus) => {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault();
    handleFilterChange(filterValue);
  }
};

// Get count text for accessibility
const getFilterAriaLabel = (option: (typeof filterOptions)[0]) => {
  const isActive = activeFilter.value === option.value;
  return `${option.label}. ${option.description}. ${isActive ? 'Currently selected' : 'Click to filter'}`;
};
</script>

<template>
  <div :class="cn('flex flex-col gap-3 sm:flex-row', props.class)">
    <!-- Filter Label -->
    <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
      <Filter class="h-4 w-4" />
      <span>Filter by status:</span>
    </div>

    <!-- Filter Buttons -->
    <div class="flex flex-wrap gap-2" role="group" aria-label="Task status filters">
      <button
        v-for="option in filterOptions"
        :key="option.value"
        type="button"
        :class="getButtonClasses(option.value)"
        :aria-label="getFilterAriaLabel(option)"
        :aria-pressed="activeFilter === option.value"
        @click="handleFilterChange(option.value)"
        @keydown="handleKeydown($event, option.value)"
      >
        <component :is="option.icon" class="h-4 w-4" />
        <span>{{ option.label }}</span>
      </button>
    </div>

    <!-- Screen Reader Only Status -->
    <div class="sr-only" aria-live="polite" aria-atomic="true">
      Currently showing {{ activeFilter === 'all' ? 'all tasks' : `${activeFilter} tasks` }}
    </div>
  </div>
</template>

<style scoped>
/* Ensure proper focus visibility */
button:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Screen reader only class */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>

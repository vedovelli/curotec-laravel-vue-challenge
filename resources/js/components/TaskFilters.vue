<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { TaskStats, TaskStatus } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface Props {
  currentFilter?: TaskStatus;
  taskStats: TaskStats;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentFilter: 'all',
  class: '',
});

// Reactive state for the current filter
const activeFilter = ref<TaskStatus>(props.currentFilter);

// Filter options configuration with counts
const filterOptions = computed(() => [
  {
    value: 'all' as TaskStatus,
    label: 'All Tasks',
    count: props.taskStats.total_tasks,
  },
  {
    value: 'pending' as TaskStatus,
    label: 'Pending',
    count: props.taskStats.pending_tasks,
  },
  {
    value: 'completed' as TaskStatus,
    label: 'Completed',
    count: props.taskStats.completed_tasks,
  },
]);

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
    'rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
    isActive
      ? 'bg-white text-gray-900 shadow-sm'
      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
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
const getFilterAriaLabel = (option: (typeof filterOptions.value)[0]) => {
  const isActive = activeFilter.value === option.value;
  return `${option.label}. ${option.count} tasks. ${isActive ? 'Currently selected' : 'Click to filter'}`;
};
</script>

<template>
  <div :class="cn('mb-6', props.class)">
    <div class="flex w-fit items-center space-x-1 rounded-lg bg-gray-100 p-1">
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
        {{ option.label }}
        <span class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">
          {{ option.count }}
        </span>
      </button>
    </div>

    <!-- Screen Reader Only Status -->
    <div class="sr-only" aria-live="polite" aria-atomic="true">
      Currently showing {{ activeFilter === 'all' ? 'all tasks' : `${activeFilter} tasks` }}
    </div>
  </div>
</template>

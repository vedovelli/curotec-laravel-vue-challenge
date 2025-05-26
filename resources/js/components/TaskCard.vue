<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { Task } from '@/types';
import { formatTaskDate, getRelativeTimeText, isOverdue } from '@/utils/date';
import { Calendar, CheckCircle2, Circle, Clock } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
  task: Task;
  class?: string;
}

const props = defineProps<Props>();

// Computed properties for styling and display
const statusBadgeClasses = computed(() => {
  const baseClasses =
    'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-colors';

  if (props.task.status === 'completed') {
    return cn(baseClasses, 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400');
  }

  if (props.task.is_overdue) {
    return cn(baseClasses, 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400');
  }

  return cn(
    baseClasses,
    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
  );
});

const statusIcon = computed(() => {
  return props.task.status === 'completed' ? CheckCircle2 : Circle;
});

const dueDateText = computed(() => {
  return formatTaskDate(props.task.due_date);
});

const relativeTimeText = computed(() => {
  return getRelativeTimeText(props.task.due_date, props.task.is_completed);
});

const isTaskOverdue = computed(() => {
  return isOverdue(props.task.due_date, props.task.is_completed);
});

const cardClasses = computed(() => {
  const baseClasses =
    'group relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900';

  if (props.task.status === 'completed') {
    return cn(baseClasses, 'opacity-75 hover:opacity-100');
  }

  return baseClasses;
});

const handleCardClick = () => {
  // This will be implemented when we add navigation
  console.log('Navigate to task:', props.task.id);
};

const handleCardKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault();
    handleCardClick();
  }
};
</script>

<template>
  <div
    :class="cn(cardClasses, props.class)"
    tabindex="0"
    role="button"
    :aria-label="`View task: ${task.title}`"
    @click="handleCardClick"
    @keydown="handleCardKeydown"
  >
    <!-- Task Header -->
    <div class="mb-3 flex items-start justify-between gap-3">
      <div class="min-w-0 flex-1">
        <h3
          class="line-clamp-2 text-base font-semibold text-gray-900 transition-colors group-hover:text-blue-600 sm:text-lg dark:text-gray-100 dark:group-hover:text-blue-400"
        >
          {{ task.title }}
        </h3>
      </div>

      <!-- Status Badge -->
      <div :class="statusBadgeClasses">
        <component :is="statusIcon" class="h-3 w-3" />
        <span>{{ task.status_text }}</span>
      </div>
    </div>

    <!-- Task Description -->
    <div v-if="task.description" class="mb-4">
      <p class="line-clamp-3 text-sm text-gray-600 dark:text-gray-300">
        {{ task.description }}
      </p>
    </div>

    <!-- Task Footer -->
    <div
      class="flex flex-col gap-2 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between dark:text-gray-400"
    >
      <!-- Due Date -->
      <div v-if="task.due_date" class="flex items-center gap-1.5">
        <Calendar class="h-3 w-3" />
        <span>{{ dueDateText }}</span>
        <span
          v-if="relativeTimeText"
          :class="
            cn(
              'font-medium',
              isTaskOverdue ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'
            )
          "
        >
          ({{ relativeTimeText }})
        </span>
      </div>

      <!-- Created Date -->
      <div class="flex items-center gap-1.5">
        <Clock class="h-3 w-3" />
        <span>Created {{ formatTaskDate(task.created_at) }}</span>
      </div>
    </div>

    <!-- Hover Indicator -->
    <div
      class="pointer-events-none absolute inset-0 rounded-lg border-2 border-transparent transition-colors group-hover:border-blue-200 dark:group-hover:border-blue-800"
    />
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

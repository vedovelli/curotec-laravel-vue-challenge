<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { Task } from '@/types';
import { formatTaskDate, getRelativeTimeText, isOverdue } from '@/utils/date';
import { Link } from '@inertiajs/vue3';
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
    return cn(baseClasses, 'bg-green-100 text-green-800');
  }

  if (props.task.is_overdue) {
    return cn(baseClasses, 'bg-destructive/10 text-destructive');
  }

  return cn(baseClasses, 'bg-yellow-100 text-yellow-800');
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
    'group relative block bg-background border border-border rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2';

  if (props.task.status === 'completed') {
    return cn(baseClasses, 'opacity-75 hover:opacity-100');
  }

  return baseClasses;
});
</script>

<template>
  <Link
    :href="route('tasks.show', task.id)"
    :class="cn(cardClasses, props.class)"
    :aria-label="`View task: ${task.title}`"
  >
    <!-- Task Header -->
    <div class="mb-3 flex items-start justify-between gap-3">
      <div class="min-w-0 flex-1">
        <h3
          class="text-foreground group-hover:text-primary line-clamp-2 text-base font-semibold transition-colors sm:text-lg"
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
      <p class="text-muted-foreground line-clamp-3 text-sm">
        {{ task.description }}
      </p>
    </div>

    <!-- Task Footer -->
    <div
      class="text-muted-foreground flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between"
    >
      <!-- Due Date -->
      <div v-if="task.due_date" class="flex items-center gap-1.5">
        <Calendar class="h-3 w-3" />
        <span>{{ dueDateText }}</span>
        <span
          v-if="relativeTimeText"
          :class="cn('font-medium', isTaskOverdue ? 'text-destructive' : 'text-primary')"
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
      class="group-hover:border-primary/20 pointer-events-none absolute inset-0 rounded-lg border-2 border-transparent transition-colors"
    />
  </Link>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { Task } from '@/types';
import { formatTaskDate } from '@/utils/date';
import { Link } from '@inertiajs/vue3';
import { Clock, MoreVertical } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
  task: Task;
  class?: string;
}

const props = defineProps<Props>();

// Status colors mapping - updated to match prototype
const statusColors = computed(() => {
  const statusMap = {
    pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    completed: 'bg-green-50 text-green-700 border-green-200',
  };
  return statusMap[props.task.status] || 'bg-gray-50 text-gray-700 border-gray-200';
});

// Priority colors mapping - updated to match prototype
const priorityColors = computed(() => {
  const priority = props.task.priority_level?.toLowerCase() || 'medium';
  const priorityMap = {
    low: 'bg-green-50 text-green-700 border-green-200',
    medium: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    high: 'bg-red-50 text-red-700 border-red-200',
  };
  return priorityMap[priority as keyof typeof priorityMap] || priorityMap.medium;
});

// Status labels mapping
const statusLabels = computed(() => {
  const statusMap = {
    pending: 'Pending',
    completed: 'Completed',
  };
  return statusMap[props.task.status] || 'Unknown';
});

const dueDateText = computed(() => {
  return formatTaskDate(props.task.due_date);
});

const createdDateText = computed(() => {
  return formatTaskDate(props.task.created_at);
});

// Assignee initials for Fabio Vedovelli
const assigneeInitials = 'FV';
const assigneeName = 'Fabio Vedovelli';
</script>

<template>
  <Link
    :href="route('tasks.show', task.id)"
    :class="
      cn(
        'group block h-full cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all duration-200 hover:border-gray-300 hover:shadow-lg',
        props.class
      )
    "
    :aria-label="`View task: ${task.title}`"
  >
    <!-- Task Header with Status and Priority -->
    <div class="mb-3 flex items-start justify-between">
      <div class="flex items-center gap-2">
        <span :class="cn('rounded-full border px-2 py-1 text-xs font-medium', statusColors)">
          {{ statusLabels }}
        </span>
        <span :class="cn('rounded-full border px-2 py-1 text-xs font-medium', priorityColors)">
          {{ task.priority_level }}
        </span>
      </div>

      <button
        class="text-gray-400 opacity-0 transition-opacity group-hover:opacity-100 hover:text-gray-600"
        @click.prevent.stop
        aria-label="Task options"
      >
        <MoreVertical class="h-4 w-4" />
      </button>
    </div>

    <!-- Task Title -->
    <h3
      class="mb-2 line-clamp-2 font-semibold text-gray-900 transition-colors group-hover:text-blue-600"
    >
      {{ task.title }}
    </h3>

    <!-- Task Description -->
    <p v-if="task.description" class="mb-4 line-clamp-3 text-sm leading-relaxed text-gray-600">
      {{ task.description }}
    </p>

    <!-- Task Footer -->
    <div class="space-y-3">
      <!-- Due Date -->
      <div v-if="task.due_date" class="flex items-center text-sm text-gray-500">
        <Clock class="mr-2 h-4 w-4" />
        <span>{{ dueDateText }}</span>
      </div>

      <!-- Created Date and Assignee -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-gray-500">Created {{ createdDateText }}</span>

        <!-- Assignee Avatar -->
        <div class="flex items-center">
          <div
            class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-purple-500"
            :title="assigneeName"
          >
            <span class="text-xs font-medium text-white">
              {{ assigneeInitials }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </Link>
</template>

<template>
  <AppLayout title="Task Details">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Task Details</h2>
    </template>

    <!-- Header with navigation and actions -->
    <div class="border-b border-gray-200 bg-white px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('tasks.index')"
            class="inline-flex items-center text-gray-600 transition-colors duration-150 hover:text-gray-900"
          >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
            Back to the list
          </Link>
        </div>

        <div class="flex items-center space-x-2">
          <Link
            :href="route('tasks.edit', task.id)"
            class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white transition-colors duration-150 hover:bg-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none"
          >
            Edit Task
          </Link>
          <button
            type="button"
            :disabled="deleteForm.processing"
            class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white transition-colors duration-150 hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
            @click="handleDelete"
          >
            <span v-if="deleteForm.processing" class="mr-2">
              <svg
                class="h-4 w-4 animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
            </span>
            {{ deleteForm.processing ? 'Deleting...' : 'Delete Task' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="min-h-screen bg-gray-50">
      <div class="mx-auto max-w-4xl p-6">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
          <!-- Task Header -->
          <div class="border-b border-gray-200 px-8 py-6">
            <div class="mb-4 flex items-start justify-between">
              <h1 class="text-2xl font-bold text-gray-900">{{ task.title }}</h1>
              <span
                :class="statusClasses"
                class="flex items-center rounded-full px-3 py-1 text-sm font-medium"
              >
                <component :is="statusIcon" class="mr-2 h-4 w-4" />
                {{ task.status_text }}
              </span>
            </div>

            <p v-if="task.description" class="leading-relaxed text-gray-700">
              {{ task.description }}
            </p>
          </div>

          <!-- Task Information & Timestamps Grid -->
          <div class="grid grid-cols-1 gap-8 p-8 lg:grid-cols-2">
            <!-- Task Information -->
            <div>
              <h2 class="mb-4 text-lg font-semibold text-gray-900">Task Information</h2>
              <div class="space-y-4">
                <div class="flex items-center justify-between py-2">
                  <span class="font-medium text-gray-600">Status</span>
                  <span :class="statusClasses" class="rounded-full px-2 py-1 text-sm font-medium">
                    {{ task.status_text }}
                  </span>
                </div>

                <div class="flex items-center justify-between py-2">
                  <span class="font-medium text-gray-600">Priority</span>
                  <span
                    :class="priorityClasses"
                    class="flex items-center rounded-full border px-2 py-1 text-sm font-medium capitalize"
                  >
                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        fill-rule="evenodd"
                        d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ task.priority_level }}
                  </span>
                </div>

                <div v-if="task.due_date" class="flex items-center justify-between py-2">
                  <span class="font-medium text-gray-600">Due Date</span>
                  <div class="flex items-center text-gray-900">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <span>{{ task.formatted_due_date }}</span>
                  </div>
                </div>

                <div v-if="!task.is_completed" class="pt-4">
                  <button
                    type="button"
                    :disabled="markCompleteForm.processing"
                    class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-150 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                    @click="handleMarkComplete"
                  >
                    <span v-if="markCompleteForm.processing" class="mr-2">
                      <svg
                        class="h-4 w-4 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                      >
                        <circle
                          class="opacity-25"
                          cx="12"
                          cy="12"
                          r="10"
                          stroke="currentColor"
                          stroke-width="4"
                        ></circle>
                        <path
                          class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                      </svg>
                    </span>
                    {{ markCompleteForm.processing ? 'Updating...' : 'Mark as Complete' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div>
              <h2 class="mb-4 text-lg font-semibold text-gray-900">Timestamps</h2>
              <div class="space-y-4">
                <div>
                  <div class="flex items-center justify-between py-2">
                    <span class="font-medium text-gray-600">Created</span>
                    <div class="text-right">
                      <div class="font-medium text-gray-900">
                        {{ formatDateOnly(task.created_at) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        at {{ formatTimeOnly(task.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <div class="flex items-center justify-between py-2">
                    <span class="font-medium text-gray-600">Last Updated</span>
                    <div class="text-right">
                      <div class="font-medium text-gray-900">
                        {{ formatDateOnly(task.updated_at) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        at {{ formatTimeOnly(task.updated_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Task Details Section -->
          <div class="border-t border-gray-200 px-8 py-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Task Details</h3>
            <div class="rounded-lg bg-gray-50 p-4">
              <p class="whitespace-pre-wrap text-gray-700">
                {{ task.description || 'No additional details provided for this task.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, h } from 'vue';

interface Task {
  id: number;
  title: string;
  description: string | null;
  status: string;
  status_text: string;
  due_date: string | null;
  formatted_due_date: string | null;
  is_completed: boolean;
  is_overdue: boolean;
  days_until_due: number | null;
  priority_level: string;
  created_at: string;
  updated_at: string;
}

interface Props {
  task: Task;
}

const props = defineProps<Props>();

// Status icon components
const CircleIcon = () =>
  h(
    'svg',
    {
      class: 'w-4 h-4',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24',
    },
    [
      h('circle', {
        cx: '12',
        cy: '12',
        r: '10',
        'stroke-width': '2',
      }),
    ]
  );

const PlayCircleIcon = () =>
  h(
    'svg',
    {
      class: 'w-4 h-4',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24',
    },
    [
      h('circle', {
        cx: '12',
        cy: '12',
        r: '10',
        'stroke-width': '2',
      }),
      h('polygon', {
        points: '10,8 16,12 10,16',
        fill: 'currentColor',
      }),
    ]
  );

const CheckCircleIcon = () =>
  h(
    'svg',
    {
      class: 'w-4 h-4',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24',
    },
    [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
      }),
    ]
  );

const statusClasses = computed(() => {
  const statusMap: Record<string, string> = {
    pending: 'bg-gray-100 text-gray-800',
    'in-progress': 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
  };

  return statusMap[props.task.status] || 'bg-gray-100 text-gray-800';
});

const priorityClasses = computed(() => {
  const priorityMap: Record<string, string> = {
    low: 'bg-green-100 text-green-800 border-green-200',
    medium: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    high: 'bg-red-100 text-red-800 border-red-200',
  };

  const priority = props.task.priority_level.toLowerCase();
  return priorityMap[priority] || 'bg-gray-100 text-gray-800 border-gray-200';
});

const statusIcon = computed(() => {
  const iconMap: Record<string, any> = {
    pending: CircleIcon,
    'in-progress': PlayCircleIcon,
    completed: CheckCircleIcon,
  };

  return iconMap[props.task.status] || CircleIcon;
});

const markCompleteForm = useForm({
  status: 'completed',
});

const deleteForm = useForm({});

const handleMarkComplete = (): void => {
  markCompleteForm.put(route('tasks.update', props.task.id), {
    preserveScroll: true,
  });
};

const handleDelete = (): void => {
  if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
    deleteForm.delete(route('tasks.destroy', props.task.id));
  }
};

const formatDateOnly = (dateString: string): string => {
  const date = new Date(dateString);
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Created Today';
  } else if (date.toDateString() === yesterday.toDateString()) {
    return 'Created Yesterday';
  } else {
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  }
};

const formatTimeOnly = (dateString: string): string => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

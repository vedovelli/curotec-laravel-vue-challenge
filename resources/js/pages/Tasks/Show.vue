<template>
  <AppLayout title="Task Details">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Task Details</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-background overflow-hidden shadow-sm sm:rounded-lg">
          <div class="text-foreground p-6">
            <!-- Task Header -->
            <div class="mb-6">
              <div class="flex items-center justify-between">
                <h1 class="text-foreground text-3xl font-bold">
                  {{ task.title }}
                </h1>
                <div class="flex items-center space-x-4">
                  <span
                    :class="statusClasses"
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  >
                    {{ task.status_text }}
                  </span>
                  <span
                    v-if="task.is_overdue"
                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                  >
                    Overdue
                  </span>
                </div>
              </div>
            </div>

            <!-- Task Details -->
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <h3 class="text-foreground mb-3 text-lg font-medium">Task Information</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-muted-foreground text-sm font-medium">Status</dt>
                    <dd class="text-foreground text-sm">{{ task.status_text }}</dd>
                  </div>
                  <div v-if="task.due_date">
                    <dt class="text-muted-foreground text-sm font-medium">Due Date</dt>
                    <dd class="text-foreground text-sm">{{ task.formatted_due_date }}</dd>
                  </div>
                  <div v-if="task.days_until_due !== null">
                    <dt class="text-muted-foreground text-sm font-medium">Days Until Due</dt>
                    <dd class="text-foreground text-sm">
                      {{ task.days_until_due > 0 ? `${task.days_until_due} days` : 'Due today' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-muted-foreground text-sm font-medium">Priority</dt>
                    <dd class="text-foreground text-sm">{{ task.priority_level }}</dd>
                  </div>
                </dl>
              </div>

              <div>
                <h3 class="text-foreground mb-3 text-lg font-medium">Timestamps</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-muted-foreground text-sm font-medium">Created</dt>
                    <dd class="text-foreground text-sm">{{ formatDate(task.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-muted-foreground text-sm font-medium">Last Updated</dt>
                    <dd class="text-foreground text-sm">{{ formatDate(task.updated_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Description -->
            <div v-if="task.description" class="mb-6">
              <h3 class="text-foreground mb-3 text-lg font-medium">Description</h3>
              <div class="bg-muted rounded-lg p-4">
                <p class="text-muted-foreground whitespace-pre-wrap">{{ task.description }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="border-border flex items-center justify-between border-t pt-6">
              <Link
                :href="route('tasks.index')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out hover:bg-gray-400 focus:bg-gray-400 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-500"
              >
                ← Back to the list
              </Link>

              <div class="flex space-x-3">
                <Link
                  :href="route('tasks.edit', task.id)"
                  class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-indigo-700 focus:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-indigo-900"
                >
                  Edit Task
                </Link>
                <button
                  v-if="!task.is_completed"
                  type="button"
                  :disabled="markCompleteForm.processing"
                  class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none active:bg-green-900 disabled:opacity-25"
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
                <button
                  type="button"
                  :disabled="deleteForm.processing"
                  class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-700 focus:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-900 disabled:opacity-25"
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
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const statusClasses = computed(() => {
  return props.task.is_completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
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

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

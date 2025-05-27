<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface TaskStats {
  total_tasks: number;
  completed_tasks: number;
  pending_tasks: number;
  completion_percentage: number;
}

interface Task {
  id: number;
  title: string;
  description: string;
  status: string;
  status_text: string;
  priority_level?: string;
  due_date: string | null;
  formatted_due_date: string | null;
  is_completed: boolean;
  is_overdue: boolean;
  days_until_due: number | null;
  created_at: string;
  updated_at: string;
}

interface TaskCollection {
  data: Task[];
}

interface DashboardProps {
  tasks: TaskCollection;
  stats: TaskStats;
}

const props = defineProps<DashboardProps>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// Extract tasks from the data wrapper
const taskList = computed(() => props.tasks?.data || []);

const stats = computed(() => [
  {
    title: 'Total Tasks',
    value: props.stats?.total_tasks || 0,
    icon: '📋',
    description: 'All tasks in the system',
    color: 'text-blue-600 dark:text-blue-400',
    bgColor: 'bg-blue-50 dark:bg-blue-900/20',
  },
  {
    title: 'Completed Tasks',
    value: props.stats?.completed_tasks || 0,
    icon: '✅',
    description: 'Successfully finished',
    color: 'text-green-600 dark:text-green-400',
    bgColor: 'bg-green-50 dark:bg-green-900/20',
  },
  {
    title: 'Pending Tasks',
    value: props.stats?.pending_tasks || 0,
    icon: '⏳',
    description: 'Awaiting completion',
    color: 'text-amber-600 dark:text-amber-400',
    bgColor: 'bg-amber-50 dark:bg-amber-900/20',
  },
]);

const completionPercentage = computed(() => {
  const percentage = props.stats?.completion_percentage;
  if (percentage === undefined || percentage === null || isNaN(percentage)) {
    return 0;
  }
  return Math.round(percentage);
});

const safeCompletedTasks = computed(() => props.stats?.completed_tasks || 0);
const safePendingTasks = computed(() => props.stats?.pending_tasks || 0);

const getStatusBadgeClass = (status: string) => {
  switch (status.toLowerCase()) {
    case 'completed':
      return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
    case 'pending':
      return 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400';
    case 'in_progress':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
  }
};

const getPriorityBadgeClass = (priority: string) => {
  switch (priority?.toLowerCase()) {
    case 'high':
    case 'urgent':
      return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
    case 'medium':
    case 'normal':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
    case 'low':
      return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
    case 'completed':
      return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
  }
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'No due date';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const formatPriorityText = (priority: string) => {
  return priority?.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase()) || 'Normal';
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
      <!-- Stats Cards -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div
          v-for="stat in stats"
          :key="stat.title"
          :class="[
            'relative overflow-hidden rounded-xl border p-6 transition-all duration-200 hover:shadow-md',
            'border-sidebar-border/70 dark:border-sidebar-border',
            stat.bgColor,
          ]"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-muted-foreground text-sm font-medium">{{ stat.title }}</p>
              <p :class="['text-3xl font-bold', stat.color]">{{ stat.value }}</p>
              <p class="text-muted-foreground mt-1 text-xs">{{ stat.description }}</p>
            </div>
            <div :class="['text-4xl opacity-20']">{{ stat.icon }}</div>
          </div>
        </div>
      </div>

      <!-- Completion Progress Card -->
      <div
        class="border-sidebar-border/70 dark:border-sidebar-border bg-background relative overflow-hidden rounded-xl border p-6"
      >
        <div class="mb-4 flex items-center justify-between">
          <div>
            <h3 class="text-foreground text-lg font-semibold">Task Completion Progress</h3>
            <p class="text-muted-foreground text-sm">Overall progress across all tasks</p>
          </div>
          <div class="text-right">
            <div class="text-primary text-3xl font-bold">{{ completionPercentage }}%</div>
            <div class="text-muted-foreground text-xs">Complete</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-muted mb-4 h-3 w-full rounded-full">
          <div
            class="bg-primary h-3 rounded-full transition-all duration-500 ease-out"
            :style="{ width: `${completionPercentage}%` }"
          ></div>
        </div>

        <!-- Progress Details -->
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div class="flex items-center gap-2">
            <div class="h-3 w-3 rounded-full bg-green-500"></div>
            <span class="text-muted-foreground">Completed:</span>
            <span class="text-foreground font-medium">{{ safeCompletedTasks }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="h-3 w-3 rounded-full bg-amber-500"></div>
            <span class="text-muted-foreground">Pending:</span>
            <span class="text-foreground font-medium">{{ safePendingTasks }}</span>
          </div>
        </div>
      </div>

      <!-- Recent Tasks Table -->
      <div
        class="border-sidebar-border/70 dark:border-sidebar-border bg-background relative overflow-hidden rounded-xl border"
      >
        <div class="p-6 pb-0">
          <div class="mb-4 flex items-center justify-between">
            <div>
              <h3 class="text-foreground text-lg font-semibold">Recent Tasks</h3>
              <p class="text-muted-foreground text-sm">Latest 5 tasks from your project</p>
            </div>
            <a
              href="/tasks"
              class="text-primary hover:text-primary/80 text-sm font-medium transition-colors"
            >
              View all →
            </a>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="border-border border-b">
              <tr class="text-left">
                <th
                  class="text-muted-foreground px-6 py-3 text-xs font-medium tracking-wider uppercase"
                >
                  Task
                </th>
                <th
                  class="text-muted-foreground px-6 py-3 text-xs font-medium tracking-wider uppercase"
                >
                  Priority
                </th>
                <th
                  class="text-muted-foreground px-6 py-3 text-xs font-medium tracking-wider uppercase"
                >
                  Status
                </th>
                <th
                  class="text-muted-foreground px-6 py-3 text-xs font-medium tracking-wider uppercase"
                >
                  Due Date
                </th>
                <th
                  class="text-muted-foreground px-6 py-3 text-xs font-medium tracking-wider uppercase"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-border divide-y">
              <tr
                v-for="task in taskList"
                :key="task.id"
                class="hover:bg-muted/50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div>
                    <div class="text-foreground text-sm font-medium">{{ task.title }}</div>
                    <div class="text-muted-foreground mt-1 max-w-xs truncate text-xs">
                      {{ task.description || 'No description' }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                      getPriorityBadgeClass(task.priority_level || 'normal'),
                    ]"
                  >
                    {{ formatPriorityText(task.priority_level || 'normal') }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                      getStatusBadgeClass(task.status),
                    ]"
                  >
                    {{ task.status_text || task.status.replace('_', ' ') }}
                  </span>
                </td>
                <td class="text-muted-foreground px-6 py-4 text-sm">
                  {{ task.formatted_due_date || formatDate(task.due_date) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <a
                      :href="`/tasks/${task.id}/edit`"
                      class="text-primary hover:text-primary/80 text-sm font-medium transition-colors"
                    >
                      Edit
                    </a>
                    <span class="text-muted-foreground">|</span>
                    <a
                      :href="`/tasks/${task.id}`"
                      class="text-muted-foreground hover:text-foreground text-sm font-medium transition-colors"
                    >
                      View
                    </a>
                  </div>
                </td>
              </tr>
              <tr v-if="taskList.length === 0">
                <td colspan="5" class="text-muted-foreground px-6 py-8 text-center">
                  <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl">🎉</span>
                    <span>No tasks yet!</span>
                    <span class="text-xs">Create your first task to get started.</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions -->
      <div
        class="border-sidebar-border/70 dark:border-sidebar-border bg-background relative overflow-hidden rounded-xl border p-6"
      >
        <h3 class="text-foreground mb-4 text-lg font-semibold">Quick Actions</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <a
            href="/tasks/create"
            class="border-border hover:bg-muted/50 group flex items-center gap-3 rounded-lg border p-4 transition-colors"
          >
            <div
              class="bg-primary/10 group-hover:bg-primary/20 flex h-10 w-10 items-center justify-center rounded-lg transition-colors"
            >
              <span class="text-primary text-xl">➕</span>
            </div>
            <div>
              <div class="text-foreground font-medium">Create Task</div>
              <div class="text-muted-foreground text-sm">Add a new task</div>
            </div>
          </a>

          <a
            href="/tasks"
            class="border-border hover:bg-muted/50 group flex items-center gap-3 rounded-lg border p-4 transition-colors"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/10 transition-colors group-hover:bg-blue-500/20"
            >
              <span class="text-xl text-blue-500">📋</span>
            </div>
            <div>
              <div class="text-foreground font-medium">View All Tasks</div>
              <div class="text-muted-foreground text-sm">Manage your tasks</div>
            </div>
          </a>

          <a
            href="/tasks?status=pending"
            class="border-border hover:bg-muted/50 group flex items-center gap-3 rounded-lg border p-4 transition-colors"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 transition-colors group-hover:bg-amber-500/20"
            >
              <span class="text-xl text-amber-500">⏳</span>
            </div>
            <div>
              <div class="text-foreground font-medium">Pending Tasks</div>
              <div class="text-muted-foreground text-sm">{{ safePendingTasks }} tasks waiting</div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import TaskList from '@/components/TaskList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginatedTasks, TaskStats, TaskStatus } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
  tasks: PaginatedTasks;
  currentFilter: TaskStatus;
  taskStats: TaskStats;
}

const props = defineProps<Props>();
</script>

<template>
  <Head title="Tasks" />

  <AppLayout title="Tasks">
    <div class="min-h-full bg-gray-50 p-8">
      <div class="mb-4 flex items-center justify-between border-b border-black/15 pb-3">
        <div class="flex gap-2">
          <h2 class="text-xl leading-tight font-semibold text-gray-800">Tasks</h2>
          <p class="mt-1 text-sm text-gray-600">{{ props.taskStats.total_tasks }} total tasks</p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('tasks.create')"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
          >
            Add Task
          </Link>
        </div>
      </div>
      <TaskList
        :tasks="props.tasks"
        :current-filter="props.currentFilter"
        :task-stats="props.taskStats"
      />
    </div>
  </AppLayout>
</template>

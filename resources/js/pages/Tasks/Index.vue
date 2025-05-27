<script setup lang="ts">
import TaskList from '@/components/TaskList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginatedTasks, TaskStatus } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Props {
  tasks: PaginatedTasks;
  currentFilter: TaskStatus;
}

const props = defineProps<Props>();
</script>

<template>
  <Head title="Tasks" />

  <AppLayout title="Tasks">
    <template #header>
      <h2 class="text-xl leading-tight font-semibold text-gray-800">Tasks</h2>
    </template>

    <div class="py-4">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
              <h1 class="text-2xl font-bold text-gray-900">Tasks</h1>
              <div class="text-sm text-gray-600">{{ props.tasks.meta.total }} total tasks</div>
            </div>

            <!-- Task List -->
            <TaskList :tasks="props.tasks" :current-filter="props.currentFilter" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

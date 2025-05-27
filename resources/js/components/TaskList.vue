<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { PaginatedTasks, PaginationLink, TaskStatus } from '@/types';
import { router } from '@inertiajs/vue3';
import { AlertCircle, Loader2 } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import TaskCard from './TaskCard.vue';
import TaskFilters from './TaskFilters.vue';
import { Skeleton } from './ui/skeleton';

interface Props {
  tasks: PaginatedTasks;
  currentFilter?: TaskStatus;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentFilter: 'all',
  class: '',
});

// Loading state management
const isLoading = ref(false);

// Handle Inertia loading states
onMounted(() => {
  const handleStart = () => {
    isLoading.value = true;
  };

  const handleFinish = () => {
    isLoading.value = false;
  };

  router.on('start', handleStart);
  router.on('finish', handleFinish);
});

// Computed properties
const hasResults = computed(() => props.tasks.data.length > 0);
const totalTasks = computed(() => props.tasks.meta.total);
const currentPage = computed(() => props.tasks.meta.current_page);
const lastPage = computed(() => props.tasks.meta.last_page);
const showingFrom = computed(() => props.tasks.meta.from);
const showingTo = computed(() => props.tasks.meta.to);

// Filter status text for accessibility
const filterStatusText = computed(() => {
  const filter = props.currentFilter;
  const total = totalTasks.value;

  if (filter === 'all') {
    return `Showing all ${total} tasks`;
  }

  return `Showing ${total} ${filter} tasks`;
});

// Handle pagination navigation
const handlePaginationClick = (url: string | null) => {
  if (!url) return;

  router.visit(url, {
    preserveState: true,
    preserveScroll: false,
    only: ['tasks'],
  });
};

// Handle filter reset
const handleResetFilters = () => {
  const currentUrl = new URL(window.location.href);
  const newUrl = currentUrl.pathname;

  router.visit(newUrl, {
    preserveState: true,
    preserveScroll: true,
    only: ['tasks'],
  });
};

// Pagination link classes
const getPaginationLinkClasses = (link: PaginationLink) => {
  const baseClasses =
    'relative inline-flex items-center px-4 py-2 text-sm font-medium transition-colors duration-200 focus:z-10 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2';

  if (!link.url) {
    return cn(baseClasses, 'bg-muted text-muted-foreground cursor-not-allowed');
  }

  if (link.active) {
    return cn(baseClasses, 'bg-primary text-primary-foreground hover:bg-primary/90 z-10');
  }

  return cn(baseClasses, 'border-border bg-background text-foreground hover:bg-muted border');
};

// Task grid classes
const taskGridClasses = computed(() => {
  return cn(
    'grid gap-4 sm:gap-6',
    'grid-cols-1',
    'md:grid-cols-2',
    'lg:grid-cols-3',
    'xl:grid-cols-4'
  );
});
</script>

<template>
  <div :class="cn('space-y-6', props.class)">
    <!-- Task Filters -->
    <TaskFilters :current-filter="currentFilter" />

    <!-- Loading State -->
    <div v-if="isLoading" class="space-y-4" aria-live="polite" aria-busy="true">
      <div class="text-muted-foreground flex items-center gap-2 text-sm">
        <Loader2 class="h-4 w-4 animate-spin" />
        <span>Loading tasks...</span>
      </div>

      <!-- Skeleton Loading -->
      <div :class="taskGridClasses">
        <div v-for="i in 6" :key="i" class="space-y-3">
          <Skeleton class="h-4 w-3/4" />
          <Skeleton class="h-3 w-full" />
          <Skeleton class="h-3 w-2/3" />
          <div class="flex justify-between">
            <Skeleton class="h-3 w-1/4" />
            <Skeleton class="h-3 w-1/4" />
          </div>
        </div>
      </div>
    </div>

    <!-- Task List Content -->
    <div v-else>
      <!-- Results Summary -->
      <div class="mb-4 flex items-center justify-between">
        <div class="text-muted-foreground text-sm" aria-live="polite">
          {{ filterStatusText }}
          <span v-if="hasResults && showingFrom && showingTo">
            ({{ showingFrom }}-{{ showingTo }} of {{ totalTasks }})
          </span>
        </div>
      </div>

      <!-- Task Grid -->
      <div v-if="hasResults">
        <ul :class="taskGridClasses" role="list" aria-label="Task list">
          <li v-for="task in tasks.data" :key="task.id">
            <TaskCard :task="task" />
          </li>
        </ul>
      </div>

      <!-- Empty State -->
      <div v-else class="py-12 text-center">
        <div class="bg-muted mx-auto flex h-12 w-12 items-center justify-center rounded-full">
          <AlertCircle class="text-muted-foreground h-6 w-6" />
        </div>

        <h3 class="text-foreground mt-4 text-lg font-medium">No tasks found</h3>

        <p class="text-muted-foreground mt-2 text-sm">
          <span v-if="currentFilter === 'all'"> Get started by creating your first task. </span>
          <span v-else> No {{ currentFilter }} tasks match your current filter. </span>
        </p>

        <div class="mt-6">
          <button
            v-if="currentFilter !== 'all'"
            type="button"
            class="bg-primary text-primary-foreground hover:bg-primary/90 focus-visible:ring-ring inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
            @click="handleResetFilters"
          >
            Show all tasks
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="hasResults && lastPage > 1" class="mt-8">
        <nav
          class="border-border flex items-center justify-between border-t px-4 sm:px-0"
          aria-label="Pagination"
        >
          <!-- Mobile Pagination -->
          <div class="flex w-0 flex-1 sm:hidden">
            <button
              v-if="tasks.links[0]?.url"
              type="button"
              :class="getPaginationLinkClasses(tasks.links[0])"
              @click="handlePaginationClick(tasks.links[0].url)"
            >
              Previous
            </button>
          </div>

          <!-- Desktop Pagination -->
          <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
              <p class="text-foreground text-sm">
                Showing
                <span class="font-medium">{{ showingFrom }}</span>
                to
                <span class="font-medium">{{ showingTo }}</span>
                of
                <span class="font-medium">{{ totalTasks }}</span>
                results
              </p>
            </div>

            <div>
              <nav
                class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                aria-label="Pagination"
              >
                <button
                  v-for="(link, index) in tasks.links"
                  :key="index"
                  type="button"
                  :class="[
                    getPaginationLinkClasses(link),
                    index === 0 && 'rounded-l-md',
                    index === tasks.links.length - 1 && 'rounded-r-md',
                  ]"
                  :aria-current="link.active ? 'page' : undefined"
                  :aria-label="
                    link.active ? `Current page, page ${link.label}` : `Go to page ${link.label}`
                  "
                  :disabled="!link.url"
                  @click="handlePaginationClick(link.url)"
                  v-html="link.label"
                />
              </nav>
            </div>
          </div>

          <!-- Mobile Next Button -->
          <div class="flex w-0 flex-1 justify-end sm:hidden">
            <button
              v-if="tasks.links[tasks.links.length - 1]?.url"
              type="button"
              :class="getPaginationLinkClasses(tasks.links[tasks.links.length - 1])"
              @click="handlePaginationClick(tasks.links[tasks.links.length - 1].url)"
            >
              Next
            </button>
          </div>
        </nav>
      </div>
    </div>

    <!-- Screen Reader Status -->
    <div class="sr-only" aria-live="polite" aria-atomic="true">
      <span v-if="isLoading">Loading tasks</span>
      <span v-else-if="hasResults">
        {{ filterStatusText }}. Page {{ currentPage }} of {{ lastPage }}.
      </span>
      <span v-else>No tasks found for current filter.</span>
    </div>
  </div>
</template>

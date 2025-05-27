<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { PaginatedTasks, TaskStats, TaskStatus } from '@/types';
import { router } from '@inertiajs/vue3';
import { ClipboardList } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import TaskCard from './TaskCard.vue';
import TaskFilters from './TaskFilters.vue';

interface Props {
  tasks: PaginatedTasks;
  taskStats: TaskStats;
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
const totalTasks = computed(() => props.tasks.total);
const currentPage = computed(() => props.tasks.current_page);
const lastPage = computed(() => props.tasks.last_page);
const showingFrom = computed(() => props.tasks.from);
const showingTo = computed(() => props.tasks.to);

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
const getPaginationLinkClasses = (link: { url: string | null; active: boolean }) => {
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
  return cn('grid gap-6', 'grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3', 'xl:grid-cols-4');
});

// Get previous and next links for mobile pagination
const previousLink = computed(() =>
  props.tasks.links.find((link) => link.label.includes('Previous'))
);
const nextLink = computed(() => props.tasks.links.find((link) => link.label.includes('Next')));
</script>

<template>
  <div :class="cn('space-y-6', props.class)">
    <!-- Task Filters -->
    <TaskFilters :current-filter="currentFilter" :task-stats="taskStats" />

    <!-- Task List Content -->
    <div>
      <!-- Task Grid -->
      <div v-if="hasResults">
        <ul :class="taskGridClasses" role="list" aria-label="Task list">
          <li v-for="task in tasks.data" :key="task.id">
            <TaskCard :task="task" />
          </li>
        </ul>
      </div>

      <!-- Empty State -->
      <div v-else class="py-16 text-center">
        <div
          class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-200"
        >
          <ClipboardList class="h-8 w-8 text-gray-500" />
        </div>
        <h3 class="mb-2 text-lg font-medium text-gray-900">No tasks found</h3>
        <p class="text-gray-600">
          <span v-if="currentFilter === 'all'">
            Try adjusting your filters or create a new task.
          </span>
          <span v-else> Try adjusting your filters or create a new task. </span>
        </p>
      </div>

      <!-- Pagination -->
      <div v-if="hasResults && lastPage > 1" class="mt-12">
        <nav
          class="flex items-center justify-between border-t px-4 sm:px-0"
          aria-label="Pagination"
        >
          <!-- Mobile Pagination -->
          <div class="flex w-0 flex-1 sm:hidden">
            <button
              v-if="previousLink?.url"
              type="button"
              :class="getPaginationLinkClasses(previousLink)"
              @click="handlePaginationClick(previousLink.url)"
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
              v-if="nextLink?.url"
              type="button"
              :class="getPaginationLinkClasses(nextLink)"
              @click="handlePaginationClick(nextLink.url)"
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

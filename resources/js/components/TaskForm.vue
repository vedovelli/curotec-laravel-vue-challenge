<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto max-w-2xl">
      <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-900">
            {{ isCreateMode ? 'Create New Task' : 'Edit Task' }}
          </h1>
        </div>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <!-- Task Title -->
          <div class="space-y-2">
            <label for="title" class="block text-sm font-medium text-gray-700">
              Task Title <span class="text-red-500">*</span>
            </label>
            <input
              id="title"
              v-model="localForm.title"
              type="text"
              name="title"
              required
              placeholder="Enter task title"
              class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              :class="{
                'border-red-300 focus:border-red-500 focus:ring-red-500': props.form.errors.title,
              }"
            />
            <InputError :message="props.form.errors.title" />
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <label for="description" class="block text-sm font-medium text-gray-700">
              Description
            </label>
            <textarea
              id="description"
              v-model="localForm.description"
              name="description"
              rows="4"
              placeholder="Enter task description (optional)"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              :class="{
                'border-red-300 focus:border-red-500 focus:ring-red-500':
                  props.form.errors.description,
              }"
            />
            <InputError :message="props.form.errors.description" />
          </div>

          <!-- Status -->
          <div class="space-y-2">
            <label for="status" class="block text-sm font-medium text-gray-700">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              id="status"
              v-model="localForm.status"
              name="status"
              required
              class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              :class="{
                'border-red-300 focus:border-red-500 focus:ring-red-500': props.form.errors.status,
              }"
            >
              <option value="">Select status</option>
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
            <InputError :message="props.form.errors.status" />
          </div>

          <!-- Priority -->
          <div class="space-y-2">
            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
            <select
              id="priority"
              v-model="localForm.priority"
              name="priority"
              class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              :class="{
                'border-red-300 focus:border-red-500 focus:ring-red-500':
                  props.form.errors.priority,
              }"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
            <InputError :message="props.form.errors.priority" />
          </div>

          <!-- Due Date -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Due Date</label>
            <div class="relative">
              <button
                type="button"
                @click="toggleDatePicker"
                class="flex w-full items-center justify-between rounded-md border border-gray-300 bg-white px-3 py-2 text-left shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                :class="{
                  'border-red-300 focus:border-red-500 focus:ring-red-500':
                    props.form.errors.due_date,
                }"
              >
                <span :class="localForm.due_date ? 'text-gray-900' : 'text-gray-500'">
                  {{ localForm.due_date ? formatDisplayDate(localForm.due_date) : 'dd/mm/yyyy' }}
                </span>
                <svg
                  class="h-4 w-4 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </button>

              <div
                v-if="showDatePicker"
                class="absolute top-full left-0 z-10 mt-1 rounded-md border border-gray-300 bg-white p-4 shadow-lg"
              >
                <input
                  type="date"
                  :value="localForm.due_date"
                  :min="isCreateMode ? today : undefined"
                  @change="handleDateSelect"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <button
                  type="button"
                  @click="closeDatePicker"
                  class="mt-2 text-sm text-gray-600 hover:text-gray-800"
                >
                  Close
                </button>
              </div>
            </div>
            <InputError :message="props.form.errors.due_date" />
            <p v-if="isCreateMode" class="mt-1 text-sm text-gray-500">
              Optional. Leave blank if no due date is required.
            </p>
          </div>

          <!-- Assignee -->
          <div class="space-y-2">
            <label for="assignee" class="block text-sm font-medium text-gray-700">Assignee</label>
            <input
              id="assignee"
              v-model="localForm.assignee"
              type="text"
              name="assignee"
              placeholder="Enter assignee name"
              class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              :class="{
                'border-red-300 focus:border-red-500 focus:ring-red-500':
                  props.form.errors.assignee,
              }"
            />
            <InputError :message="props.form.errors.assignee" />
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6">
            <Link
              :href="cancelRoute"
              class="rounded-md border border-gray-300 px-6 py-2 text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="props.form.processing"
              class="rounded-md bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            >
              <span v-if="props.form.processing" class="mr-2">
                <svg
                  class="mr-3 -ml-1 h-4 w-4 animate-spin text-white"
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
              {{ props.form.processing ? processingText : submitText }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Link } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

interface TaskFormData {
  title: string;
  description: string;
  status: string;
  priority: string;
  due_date: string;
  assignee: string;
  errors: Partial<Record<keyof TaskFormData, string>>;
  processing: boolean;
}

interface Props {
  form: TaskFormData;
  mode: 'create' | 'edit';
  cancelRoute: string;
  submitText?: string;
  processingText?: string;
}

const props = withDefaults(defineProps<Props>(), {
  submitText: 'Submit',
  processingText: 'Processing...',
});

const emit = defineEmits<{
  submit: [
    data: {
      title: string;
      description: string;
      status: string;
      priority: string;
      due_date: string;
      assignee: string;
    },
  ];
}>();

// Create local reactive form data to avoid prop mutation
const localForm = reactive({
  title: props.form.title,
  description: props.form.description,
  status: props.form.status,
  priority: props.form.priority || 'medium',
  due_date: props.form.due_date,
  assignee: props.form.assignee || '',
});

const showDatePicker = ref(false);

// Watch for changes in props.form and update local form
watch(
  () => props.form,
  (newForm) => {
    localForm.title = newForm.title;
    localForm.description = newForm.description;
    localForm.status = newForm.status;
    localForm.priority = newForm.priority || 'medium';
    localForm.due_date = newForm.due_date;
    localForm.assignee = newForm.assignee || '';
  },
  { deep: true }
);

const isCreateMode = computed(() => props.mode === 'create');

const today = computed(() => {
  return new Date().toISOString().split('T')[0];
});

const toggleDatePicker = (): void => {
  showDatePicker.value = !showDatePicker.value;
};

const closeDatePicker = (): void => {
  showDatePicker.value = false;
};

const handleDateSelect = (event: Event): void => {
  const target = event.target as HTMLInputElement;
  localForm.due_date = target.value;
  showDatePicker.value = false;
};

const formatDisplayDate = (dateString: string): string => {
  if (!dateString) return '';

  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

const handleSubmit = (): void => {
  emit('submit', {
    title: localForm.title,
    description: localForm.description,
    status: localForm.status,
    priority: localForm.priority,
    due_date: localForm.due_date,
    assignee: localForm.assignee,
  });
};
</script>

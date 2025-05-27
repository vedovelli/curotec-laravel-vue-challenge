<template>
  <form class="space-y-6" @submit.prevent="handleSubmit">
    <!-- Title Field -->
    <div>
      <label for="title" class="text-foreground block text-sm font-medium">
        Task Title <span class="text-red-500">*</span>
      </label>
      <div class="mt-1">
        <input
          id="title"
          v-model="localForm.title"
          type="text"
          name="title"
          required
          class="border-border block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
          :class="{
            'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500':
              props.form.errors.title,
          }"
          placeholder="Enter task title"
        />
      </div>
      <InputError :message="props.form.errors.title" />
    </div>

    <!-- Description Field -->
    <div>
      <label for="description" class="text-foreground block text-sm font-medium">
        Description
      </label>
      <div class="mt-1">
        <textarea
          id="description"
          v-model="localForm.description"
          name="description"
          rows="4"
          class="border-border block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
          :class="{
            'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500':
              props.form.errors.description,
          }"
          placeholder="Enter task description (optional)"
        />
      </div>
      <InputError :message="props.form.errors.description" />
    </div>

    <!-- Status Field -->
    <div>
      <label for="status" class="text-foreground block text-sm font-medium">
        Status <span class="text-red-500">*</span>
      </label>
      <div class="mt-1">
        <select
          id="status"
          v-model="localForm.status"
          name="status"
          required
          class="border-border block w-full rounded-md border px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
          :class="{
            'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500':
              props.form.errors.status,
          }"
        >
          <option value="">Select status</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <InputError :message="props.form.errors.status" />
    </div>

    <!-- Due Date Field -->
    <div>
      <label for="due_date" class="text-foreground block text-sm font-medium"> Due Date </label>
      <div class="mt-1">
        <input
          id="due_date"
          v-model="localForm.due_date"
          type="date"
          name="due_date"
          :min="isCreateMode ? today : undefined"
          class="border-border block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
          :class="{
            'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500':
              props.form.errors.due_date,
          }"
        />
      </div>
      <InputError :message="props.form.errors.due_date" />
      <p v-if="isCreateMode" class="mt-1 text-sm text-gray-500">
        Optional. Leave blank if no due date is required.
      </p>
    </div>

    <!-- Form Actions -->
    <div class="border-border mt-6 flex items-center justify-between border-t pt-6">
      <Link
        :href="cancelRoute"
        class="inline-flex items-center rounded-md border border-transparent bg-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out hover:bg-gray-400 focus:bg-gray-400 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-500"
      >
        Cancel
      </Link>

      <button
        type="submit"
        :disabled="props.form.processing"
        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-indigo-700 focus:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-indigo-900 disabled:opacity-50"
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
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Link } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';

interface TaskFormData {
  title: string;
  description: string;
  status: string;
  due_date: string;
  errors: Record<string, string>;
  processing: boolean;
  [key: string]: unknown;
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
  submit: [data: { title: string; description: string; status: string; due_date: string }];
}>();

// Create local reactive form data to avoid prop mutation
const localForm = reactive({
  title: props.form.title,
  description: props.form.description,
  status: props.form.status,
  due_date: props.form.due_date,
});

// Watch for changes in props.form and update local form
watch(
  () => props.form,
  (newForm) => {
    localForm.title = newForm.title;
    localForm.description = newForm.description;
    localForm.status = newForm.status;
    localForm.due_date = newForm.due_date;
  },
  { deep: true }
);

const isCreateMode = computed(() => props.mode === 'create');

const today = computed(() => {
  return new Date().toISOString().split('T')[0];
});

const handleSubmit = (): void => {
  emit('submit', {
    title: localForm.title,
    description: localForm.description,
    status: localForm.status,
    due_date: localForm.due_date,
  });
};
</script>

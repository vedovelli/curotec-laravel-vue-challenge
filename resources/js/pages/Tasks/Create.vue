<template>
  <AppLayout title="Create Task">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Create New Task</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-background overflow-hidden shadow-sm sm:rounded-lg">
          <div class="text-foreground p-6">
            <TaskForm
              :form="form"
              mode="create"
              :cancel-route="route('dashboard')"
              submit-text="Create Task"
              processing-text="Creating..."
              @submit="handleSubmit"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import TaskForm from '@/components/TaskForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface TaskFormData {
  title: string;
  description: string;
  status: string;
  due_date: string;
  [key: string]: string;
}

const form = useForm<TaskFormData>({
  title: '',
  description: '',
  status: 'pending',
  due_date: '',
});

const handleSubmit = (data: {
  title: string;
  description: string;
  status: string;
  due_date: string;
}): void => {
  // Update form with the data from TaskForm component
  form.title = data.title;
  form.description = data.description;
  form.status = data.status;
  form.due_date = data.due_date;

  form.post(route('tasks.store'), {
    onSuccess: () => {
      // Form will be automatically redirected by the controller
    },
    onError: () => {
      // Errors will be automatically handled by Inertia
    },
  });
};
</script>

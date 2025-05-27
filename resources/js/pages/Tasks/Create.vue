<template>
  <AppLayout title="Create Task">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Create New Task</h2>
    </template>

    <TaskForm
      :form="form"
      mode="create"
      :cancel-route="route('dashboard')"
      submit-text="Create Task"
      processing-text="Creating..."
      @submit="handleSubmit"
    />
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
  priority: string;
  due_date: string;
  assignee: string;
  [key: string]: string;
}

const form = useForm<TaskFormData>({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  due_date: '',
  assignee: '',
});

const handleSubmit = (data: {
  title: string;
  description: string;
  status: string;
  priority: string;
  due_date: string;
  assignee: string;
}): void => {
  // Update form with the data from TaskForm component
  form.title = data.title;
  form.description = data.description;
  form.status = data.status;
  form.priority = data.priority;
  form.due_date = data.due_date;
  form.assignee = data.assignee;

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

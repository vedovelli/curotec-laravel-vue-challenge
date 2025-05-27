<template>
  <AppLayout title="Edit Task">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Edit Task</h2>
    </template>

    <TaskForm
      :form="form"
      mode="edit"
      :cancel-route="route('tasks.show', task.id)"
      submit-text="Update Task"
      processing-text="Updating..."
      @submit="handleSubmit"
    />
  </AppLayout>
</template>

<script setup lang="ts">
import TaskForm from '@/components/TaskForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface Task {
  id: number;
  title: string;
  description: string | null;
  status: string;
  priority: string;
  due_date: string | null;
  assignee: string | null;
}

interface Props {
  task: Task;
}

const props = defineProps<Props>();

const form = useForm({
  title: props.task.title,
  description: props.task.description || '',
  status: props.task.status,
  priority: props.task.priority || 'medium',
  due_date: props.task.due_date || '',
  assignee: props.task.assignee || '',
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

  form.put(route('tasks.update', props.task.id), {
    preserveScroll: true,
  });
};
</script>

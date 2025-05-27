<template>
  <AppLayout title="Edit Task">
    <template #header>
      <h2 class="text-foreground text-xl leading-tight font-semibold">Edit Task</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-background overflow-hidden shadow-sm sm:rounded-lg">
          <div class="text-foreground p-6">
            <TaskForm
              :form="form"
              mode="edit"
              :cancel-route="route('tasks.show', task.id)"
              submit-text="Update Task"
              processing-text="Updating..."
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

interface Task {
  id: number;
  title: string;
  description: string | null;
  status: string;
  due_date: string | null;
}

interface Props {
  task: Task;
}

const props = defineProps<Props>();

const form = useForm({
  title: props.task.title,
  description: props.task.description || '',
  status: props.task.status,
  due_date: props.task.due_date || '',
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

  form.put(route('tasks.update', props.task.id), {
    preserveScroll: true,
  });
};
</script>

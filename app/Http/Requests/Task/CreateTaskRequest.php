<?php

declare(strict_types=1);

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

final class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:pending,completed'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get the custom error messages for validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required',
            'title.max' => 'The task title cannot exceed 255 characters',
            'description.max' => 'The task description cannot exceed 1000 characters',
            'status.required' => 'The task status is required',
            'status.in' => 'The task status must be either pending or completed',
            'due_date.date' => 'The due date must be a valid date',
            'due_date.after_or_equal' => 'The due date cannot be in the past',
        ];
    }
} 
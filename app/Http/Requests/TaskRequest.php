<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'string', Rule::in(Task::priorities())],
            'status' => ['sometimes', 'string', Rule::in(Task::statuses())],
        ];
    }

    public function payload(?Task $task = null): array
    {
        $data = $this->validated();

        $status = $data['status'] ?? $task?->status ?? Task::STATUS_PENDING;
        $data['status'] = $status;
        $data['completed_at'] = $status === Task::STATUS_DONE
            ? ($task?->completed_at ?? now())
            : null;

        return $data;
    }

}

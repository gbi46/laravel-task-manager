<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Task::statuses())],
        ];
    }

    public function payload(): array
    {
        $data = $this->validated();
        $data['completed_at'] = $data['status'] === Task::STATUS_DONE ? now() : null;

        return $data;
    }
}

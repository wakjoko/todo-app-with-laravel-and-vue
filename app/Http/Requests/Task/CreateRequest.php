<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'priority_id' => ['nullable', 'integer'],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'archived_at' => ['nullable', 'date'],
        ];
    }
}

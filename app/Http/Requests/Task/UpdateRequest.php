<?php

namespace App\Http\Requests\Task;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority_id' => ['nullable', 'integer'],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'archived_at' => ['nullable', 'date'],
        ];
    }

    public function toUpdate(): array
    {
        $task = $this->except('id');
        $task['due_at'] = Carbon::make($task['due_at'] ?? null);
        $task['completed_at'] = Carbon::make($task['completed_at'] ?? null);
        $task['archived_at'] = Carbon::make($task['archived_at'] ?? null);

        return $task;
    }
}

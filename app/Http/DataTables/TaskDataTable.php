<?php

namespace App\Http\DataTables;

use App\Models\Task;
use App\Filters\TaskFilter;
use Illuminate\Support\Collection;
use App\Vendors\Datatables\Request;
use Illuminate\Database\Eloquent\Builder;

class TaskDataTable extends Request
{
    /**
     * Represents the select columns in sql which will be added into final sql.
     */
    protected function columns(): array
    {
        return [
            'id' => 'integer',
            'title' => 'string',
            'priority_id' => 'integer',
            'due_at' => 'date_range',
            'completed_at' => 'datetime',
            'archived_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Represents the sql query without select statement.
     */
    protected function eloquentBuilder(): Builder
    {
        $completed = $this->boolean('completed');
        $archived = $this->boolean('archived');
        $owner = auth()->user()->id;

        return Task::query()
            ->withCount('tags', 'media')
            ->ownedBy($owner)
            ->completed($completed)
            ->archived($archived);
    }

    /**
     * Attach data filtering during serialization.
     */
    protected function serializeData(): Collection
    {
        return $this->data->map(function($model) {
            return TaskFilter::list($model);
        });
    }
}
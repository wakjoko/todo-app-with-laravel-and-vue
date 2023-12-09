<?php

namespace App\Filters;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskFilter
{
    public function __construct(protected Task $model) {}

    public static function list(Task $model): Collection
    {
        $filter = new static($model);

        $excluded = [
            'owner_id',
            'description',
            'updated_at',
            'deleted_at',
        ];

        return collect($filter->model)->except($excluded);
    }

    public static function show(Task $model): Collection
    {
        $filter = new static($model);

        $excluded = [
            'owner_id',
            'plain_text_description',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        return collect($filter->model)->except($excluded);
    }
}
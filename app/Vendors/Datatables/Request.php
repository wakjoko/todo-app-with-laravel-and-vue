<?php

namespace App\Vendors\Datatables;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Request extends FormRequest
{
    protected $data;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'columns.*.data' => ['required', 'string'],
            'draw' => ['nullable', 'integer'],
            'start' => ['nullable', 'integer'],
            'length' => ['nullable', 'integer'],
            'search.value' => ['nullable', 'string'],
            'order.*.column' => ['nullable', 'string'],
            'order.*.dir' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the translated attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'columns.*.data' => 'data in columns',
        ];
    }

    /**
     * Represents the select columns which will be added during data processing.
     */
    protected function columns(): array
    {
        /**
         * to have better flexibility when building query for date range comparison,
         * datetime data type needs further enhancement.
         * best possible way is to do it similar like form request's rules settings
         * then convert the settings to sql query builder format
         */

        return [
            // 'columnName' => 'columnDataType',
        ];
    }


    /**
     * Represents the eloquent query builder without select statement.
     */
    protected function eloquentBuilder(): Builder
    {
        return Model::query();
    }

    protected function getColumnType($key): string|null
    {
        if (! array_key_exists($key, $this->columns())) {
            return null;
        }

        return $this->columns()[$key];
    }

    /**
     * Data conversion.
     */
    protected function serializeData(): Collection
    {
        return $this->data;
    }

    /**
     * Get db datas based on datatables request.
     */
    public function results(): array
    {
        $draw = $this->get('draw') ?? 1;
        $offset = $this->get('start') ?? 0;
        $limit = $this->get('length') ?? 10;
        $search = $this->get('search')['value'] ?? null;

        // set default order column
        if (!$this->request->has('order')) {
            $this->request->add([
                'order' => [[
                    'column' => 0,
                ]],
            ]);
        }

        $recordsTotal = $this->eloquentBuilder()->count();

        $searchAnyColumns = function ($query) use ($search) {
            if (empty($search)) {
                return;
            }

            foreach ($this->get('columns') as $column) {
                $type = $this->getColumnType($column['data']);

                match($type) {
                    'string' => $query->orWhere($column['data'], 'like', "%{$search}%"),
                    'integer' => $query->orWhere($column['data'], $search),
                    default => null,
                };
            };
        };

        $searchColumns = function ($query) {
            foreach ($this->get('columns') as $column) {
                $type = $this->getColumnType($column['data']);
                $search = $column['search']['value'];

                if (empty($search)) {
                    continue;
                }

                match($type) {
                    'string' => $query->where($column['data'], 'like', "%{$search}%"),
                    'integer' => $query->where($column['data'], $search),
                    'date_range' => $query->whereBetween($column['data'], [
                                        Carbon::make($column['search']['from']),
                                        Carbon::make($column['search']['to'])
                                    ]),
                    default => null,
                };
            };
        };

        $orderByColumns = function ($query) {
            foreach ($this->get('order') as $order) {
                $column = $this->get('columns')[$order['column']]['data'];
                $direction = $order['dir'] ?? 'asc';

                $query->orderBy($column, $direction);
            }
        };

        $filteredQuery = $this->eloquentBuilder()
            ->where($searchAnyColumns)
            ->where($searchColumns);

        $recordsFiltered = $filteredQuery->count();

        $this->data = $filteredQuery
            ->offset($offset)
            ->limit($limit)
            ->tap($orderByColumns)
            ->get();

        return [
            'draw' => $draw,
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $this->serializeData(),
        ];
    }
}

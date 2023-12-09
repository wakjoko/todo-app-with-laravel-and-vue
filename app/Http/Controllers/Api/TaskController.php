<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Filters\TaskFilter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\DataTables\TaskDataTable;
use App\Http\Requests\Task\CreateRequest;
use App\Http\Requests\Task\UpdateRequest;

class TaskController extends Controller
{
    /**
     * Get list of tasks in datatables json format.
     * https://datatables.net/manual/server-side#Returned-data
     */
    public function list(TaskDataTable $dt)
    {
        $results = $dt->results();

        return response()->json($results);
    }

    /**
     * Create a task.
     */
    public function create(CreateRequest $request)
    {
        $owner = auth()->user()->id;

        $inputs = $request->validated() + ['owner_id' => $owner];

        $task = Task::create($inputs);

        return response()->json($task, 201);
    }

    /**
     * Get a task.
     */
    public function show($id)
    {
        $owner = auth()->user()->id;
        
        $task = Task::query()
            ->where('id', $id)
            ->ownedBy($owner)
            ->firstOrFail();

        $task->load('tags');

        $filtered = TaskFilter::show($task);

        return response()->json($filtered);
    }

    /**
     * Update a task.
     */
    public function update(UpdateRequest $request)
    {
        $owner = auth()->user()->id;
        
        $task = Task::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->firstOrFail();
            
        $task->update($request->toUpdate());

        $task->loadCount('tags', 'media');

        $filtered = TaskFilter::list($task);

        return response()->json($filtered);
    }

    /**
     * Toggle completed status.
     */
    public function complete(Request $request)
    {
        $completed_at = $request->boolean('status') ? now() : null;

        $owner = auth()->user()->id;

        Task::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->update(['completed_at' => $completed_at]);

        return response()->json(status: 204);
    }

    /**
     * Toggle archived status.
     */
    public function archive(Request $request)
    {
        $archived_at = $request->boolean('status') ? now() : null;

        $owner = auth()->user()->id;

        Task::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->update(['archived_at' => $archived_at]);

        return response()->json(status: 204);
    }

    /**
     * Delete a task.
     */
    public function delete(Request $request)
    {
        $owner = auth()->user()->id;

        Task::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->delete();

        return response()->json(status: 204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTagRequest;

class TagController extends Controller
{
    /**
     * Create a tag.
     */
    public function create(CreateTagRequest $request)
    {
        $tag = Tag::create($request->validated());

        return response()->json($tag);
    }

    /**
     * Delete a tag.
     */
    public function delete(Request $request)
    {
        $owner = auth()->user()->id;

        Tag::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->delete();

        return response()->json(status: 204);
    }
}

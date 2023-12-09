<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadMediaRequest;

class MediaController extends Controller
{
    /**
     * Upload a file.
     */
    public function upload(UploadMediaRequest $request)
    {
        $file = $request->file('file');

        $disk = 'public';

        $attributes = [
            'name' => $file->getClientOriginalName(),
            'disk' => $disk,
            'model_type' => Task::class,
            'model_id' => $request->integer('task_id'),
        ];

        $media = Media::create($attributes);

        $path = $file->storeAs("media/{$media->id}", $media->name, ['disk' => $disk]);

        $media->update(['path' => $path]);

        $link = Storage::disk($disk)->url($path);

        $results = ['link' => $link, 'id' => $media->id];

        return response()->json($results, 201);
    }

    /**
     * Delete a file.
     */
    public function delete(Request $request)
    {
        $owner = auth()->user()->id;

        $media = Media::query()
            ->where('id', $request->integer('id'))
            ->ownedBy($owner)
            ->firstOrFail();

        Storage::disk($media->disk)->delete($media->path);
        
        $media->delete();
        
        return response()->json([], 204);
    }
}

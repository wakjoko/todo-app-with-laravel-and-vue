<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Priority;

class PriorityController extends Controller
{
    /**
     * Get list of priorities.
     */
    public function list()
    {
        $priorities = Priority::toBase()->get();

        return response()->json($priorities);
    }
}

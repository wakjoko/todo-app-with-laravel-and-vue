<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PriorityController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'task', 'as' => 'task.'], function () {
        Route::post('list', [TaskController::class, 'list'])->name('list');
        Route::post('create', [TaskController::class, 'create'])->name('create');
        Route::get('show/{id}', [TaskController::class, 'show'])->name('show');
        Route::post('update', [TaskController::class, 'update'])->name('update');
        Route::post('complete', [TaskController::class, 'complete'])->name('complete');
        Route::post('archive', [TaskController::class, 'archive'])->name('archive');
        Route::delete('delete', [TaskController::class, 'delete'])->name('delete');
        Route::get('priorities', [TaskController::class, 'priorities'])->name('priorities');
    });

    Route::group(['prefix' => 'priority', 'as' => 'priority.'], function () {
        Route::get('list', [PriorityController::class, 'list'])->name('list');
    });

    Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
        Route::post('create', [TagController::class, 'create'])->name('create');
        Route::delete('delete', [TagController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'media', 'as' => 'media.'], function () {
        Route::post('upload', [MediaController::class, 'upload'])->name('upload');
        // download route is not needed since it's always publicly available
        Route::delete('delete', [MediaController::class, 'delete'])->name('delete');
    });
});
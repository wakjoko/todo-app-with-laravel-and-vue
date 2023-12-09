<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'name',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function scopeOwnedBy($query, $owner)
    {
        $query->whereHas('task', fn($subQuery) => $subQuery->where('owner_id', $owner));
    }
}

<?php

namespace App\Models;

use Html2Text\Html2Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'owner_id',
        'priority_id',
        'title',
        'description',
        'due_at',
        'completed_at',
        'archived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected $appends = [
        'plain_text_description',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'task_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'media', 'model_type', 'model_id');
    }

    public function scopeOwnedBy($query, $owner)
    {
        $query->where('owner_id', $owner);
    }

    public function scopeCompleted($query, bool $status = true)
    {
        $query
            ->when($status,
                fn($query) => $query->whereNotNull('completed_at'),
                fn($query) => $query->whereNull('completed_at')
            );
    }

    public function scopeArchived($query, bool $status = true)
    {
        $query
            ->when($status,
                fn($query) => $query->whereNotNull('archived_at'),
                fn($query) => $query->whereNull('archived_at')
            );
    }

    public function getPlainTextDescriptionAttribute()
    {
        return (new Html2Text($this->attributes['description'], ['do_links' => 'none']))->getText();
    }
}

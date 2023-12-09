<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'model_type',
        'model_id',
        'hash',
        'name',
        'disk',
        'path',
    ];

    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    public function scopeOwnedBy($query, $owner)
    {
        // $query->whereExists($this->owner()->getQuery()->owned($owner));
        $query->whereHas('model', fn($subQuery) => $subQuery->where('owner_id', $owner));
    }
}

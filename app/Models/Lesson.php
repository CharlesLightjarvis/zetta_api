<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'module_id',
        'description',
        'slug',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

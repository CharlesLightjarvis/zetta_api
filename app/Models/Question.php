<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Question extends Model
{
    use HasUuids;

    protected $fillable = [
        'questionable_type',
        'questionable_id',
        'question',
        'answers',
        'type',
        'difficulty',
        'points',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function questionable(): MorphTo
    {
        return $this->morphTo();
    }
}

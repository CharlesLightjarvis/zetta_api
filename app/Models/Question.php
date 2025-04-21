<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Question extends Model
{
    use HasUuids;

    protected $fillable = [
        'question',
        'answers',
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

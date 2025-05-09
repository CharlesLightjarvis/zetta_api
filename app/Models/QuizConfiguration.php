<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QuizConfiguration extends Model
{
    use HasUuids;

    protected $fillable = [
        'configurable_type',
        'configurable_id',
        'total_questions',
        'difficulty_distribution',
        'module_distribution',
        'passing_score',
        'time_limit'
    ];

    protected $casts = [
        'difficulty_distribution' => 'array',
        'module_distribution' => 'array',
    ];

    public function configurable(): MorphTo
    {
        return $this->morphTo();
    }
}

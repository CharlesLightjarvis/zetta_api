<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Certification extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'formation_id',
        'image',
        'provider',
        'validity_period',
        'level',
        'slug',
        'benefits',
        'skills',
        'best_for',
        'prerequisites',
        'link',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'certification_student', 'certification_id', 'student_id');
    }

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'skills' => 'array',
            'best_for' => 'array',
            'prerequisites' => 'array',
        ];
    }
}

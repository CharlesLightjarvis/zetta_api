<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FormationSession extends Model
{
    use HasUuids;

    protected $table = 'formation_sessions';

    protected $fillable = [
        'formation_id',
        'teacher_id',
        'course_type',
        'start_date',
        'end_date',
        'capacity',
        'enrolled_students',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_student', 'session_id', 'student_id')->withTimestamps();
    }

    // check if the session has available spots
    public function hasAvailableSpots(): bool
    {
        return $this->enrolled_students < $this->capacity;
    }
}

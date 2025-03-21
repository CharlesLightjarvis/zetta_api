<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'session_student', 'session_id', 'student_id');
    }

    // check if the session has available spots
    public function hasAvailableSpots(): bool
    {
        return $this->enrolled_students < $this->capacity;
    }
}

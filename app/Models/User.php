<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, softDeletes, HasRoles;

    protected $fillable = [
        'fullName',
        'email',
        'imageUrl',
        'password',
        'status',
        'email_verified_at',
        'bio',
        'title',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i');
    }

    /**
     * Sessions où l'utilisateur est enseignant
     */
    public function teachingSessions(): HasMany
    {
        return $this->hasMany(FormationSession::class, 'teacher_id');
    }

    /**
     * Sessions où l'utilisateur est étudiant
     */
    public function enrolledSessions(): BelongsToMany
    {
        return $this->belongsToMany(FormationSession::class, 'session_student', 'student_id', 'session_id');
    }

    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class, 'certification_student', 'student_id', 'certification_id');
    }
}

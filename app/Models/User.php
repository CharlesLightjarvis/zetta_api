<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
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

    public function formationsAsTeacher()
    {
        return $this->hasMany(Formation::class, 'teacher_id');
    }

    public function certifications()
    {
        return $this->belongsToMany(Certification::class, 'certification_student', 'student_id', 'certification_id');
    }

    public function formationsAsStudent()
    {
        return $this->belongsToMany(Formation::class, 'formation_student', 'student_id', 'formation_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'level',
        'duration',
        'price',
        'capacity',
        'enrolled_students',
        'teacher_id',
        'category_id',
        'course_type',
        'end_date',
        'start_date',
        'prerequisites',
        'objectives',
    ];

    public function getLinkAttribute()
    {
        return "/formations/" . $this->slug;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'formation_student', 'formation_id', 'student_id');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }


    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    protected function casts(): array
    {
        return [
            'end_date' => 'date',
            'start_date' => 'date',
            'prerequisites' => 'array',
            'objectives' => 'array',
        ];
    }
}

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
        'category_id',
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

    public function sessions()
    {
        return $this->hasMany(FormationSession::class);
    }

    public function interests()
    {
        return $this->hasMany(FormationInterest::class);
    }

    protected function casts(): array
    {
        return [
            'prerequisites' => 'array',
            'objectives' => 'array',
        ];
    }
}

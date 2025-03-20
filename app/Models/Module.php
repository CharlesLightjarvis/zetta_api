<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasUuids;

    protected $fillable = [
        'formation_id',
        'name',
        'slug',
        'description',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}

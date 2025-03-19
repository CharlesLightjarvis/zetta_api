<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model
{
    use HasUuids;

    protected $fillable = [
        'formation_id',
        'certification_id',
        'description',
        'order',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withDefault();
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class)->withDefault();
    }
}

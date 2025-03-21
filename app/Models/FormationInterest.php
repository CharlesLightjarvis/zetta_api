<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FormationInterest extends Model
{
    use HasUuids;

    protected $fillable = [
        'formation_id',
        'fullName',
        'email',
        'phone',
        'message',
        'status'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}

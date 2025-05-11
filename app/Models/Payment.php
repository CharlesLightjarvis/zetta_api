<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'student_id',
        'session_id',
        'amount',
        'remaining_amount',
        'payment_method',
        'status',
        'notes',
        'payment_date'  
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(FormationSession::class);
    }

    // Méthode pour accéder à la formation via la session
    public function formation()
    {
        return $this->session->formation;
    }
}

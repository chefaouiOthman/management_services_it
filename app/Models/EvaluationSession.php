<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSession extends Model
{
    use HasFactory;

    protected $table = 'evaluation_sessions';

    protected $fillable = [
        'session_formation_id',
        'user_id',
        'note_pedagogie',
        'note_technique',
        'avis_textuel',
    ];

    protected $casts = [
        'note_pedagogie' => 'integer',
        'note_technique' => 'integer',
    ];

    public function sessionFormation()
    {
        return $this->belongsTo(SessionFormation::class, 'session_formation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

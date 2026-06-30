<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $table = 'inscriptions';

    protected $fillable = [
        'user_id',
        'session_formation_id',
        'statut_inscription',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sessionFormation()
    {
        return $this->belongsTo(SessionFormation::class, 'session_formation_id');
    }
}

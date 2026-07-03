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

    protected $casts = [];

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // =========================================================

    /** Apprenant inscrit à cette session */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Session de formation concernée */
    public function sessionFormation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SessionFormation::class, 'session_formation_id');
    }
}

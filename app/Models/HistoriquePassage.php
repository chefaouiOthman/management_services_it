<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePassage extends Model
{
    use HasFactory;

    protected $table = 'historique_passages';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'user_id',
        'zone_id',
        'horodatage',
        'tentative_statut',
    ];

    /**
     * Casts : horodatage est un datetime précis.
     */
    protected $casts = [
        'horodatage' => 'datetime',
    ];

    // =========================================================
    // RELATIONS MODULE 2 : SÉCURITÉ PHYSIQUE
    // =========================================================

    /**
     * L'utilisateur ayant tenté le passage.
     * La FK 'user_id' pointe vers 'users.id'.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * La zone concernée par la tentative de passage.
     * La FK 'zone_id' pointe vers 'zones.id'.
     */
    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}

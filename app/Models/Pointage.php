<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;

    protected $table = 'pointages';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'user_id',
        'date_jour',
        'heure_arrivee',
        'heure_depart',
        'statut_presence',
    ];

    /**
     * Casts : date pour date_jour, datetime pour les heures d'arrivée/départ.
     */
    protected $casts = [
        'date_jour'     => 'date',
        'heure_arrivee' => 'datetime',
        'heure_depart'  => 'datetime',
    ];

    // =========================================================
    // RELATIONS MODULE 2 : RH & SÉCURITÉ
    // =========================================================

    /**
     * L'utilisateur (employé ou stagiaire) auquel appartient ce pointage.
     * La FK 'user_id' pointe vers 'users.id'.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'nom_complet' => 'Utilisateur inconnu',
            'name'        => 'Inconnu',
            'email'       => 'N/A',
        ]);
    }
}

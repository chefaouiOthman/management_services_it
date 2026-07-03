<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'code_zone',
        'nom_salle',
        'niveau_requis',
        'est_active',
    ];

    /**
     * Casts : integer pour niveau_requis, boolean pour est_active.
     */
    protected $casts = [
        'niveau_requis' => 'integer',
        'est_active'    => 'boolean',
    ];

    // =========================================================
    // RELATIONS MODULE 2 : SÉCURITÉ PHYSIQUE
    // =========================================================

    /**
     * Historique de toutes les tentatives de passage dans cette zone.
     * La FK 'zone_id' pointe vers 'zones.id'.
     */
    public function historiquePassages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistoriquePassage::class, 'zone_id');
    }

    // =========================================================
    // NOTE ARCHITECTURALE :
    // La relation usersWithAccess() via la table pivot 'user_zone'
    // a été supprimée car la table 'user_zone' n'est pas définie
    // dans GEMINI.md et a été retirée lors de l'audit des migrations.
    // Le contrôle d'accès aux zones se fait via 'niveau_requis'
    // comparé au niveau de l'utilisateur, pas via une table pivot.
    // =========================================================
}

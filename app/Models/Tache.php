<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $table = 'taches';

    protected $fillable = [
        'titre_tache',
    ];

    protected $casts = [];

    // =========================================================
    // RELATIONS MODULE 3 : PRODUCTION (côté inverse des pivots)
    // =========================================================

    /**
     * Projets auxquels cette tâche est rattachée (via pivot projet_tache).
     * withPivot() expose les attributs priorite et statut_tache.
     */
    public function projets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Projet::class,
            'projet_tache', // table pivot
            'tache_id',     // FK de ce modèle dans le pivot
            'projet_id'     // FK du modèle lié dans le pivot
        )
        ->withPivot(['priorite', 'statut_tache'])
        ->withTimestamps();
    }

    /**
     * Feuilles de temps liées à cette tâche (via pivot feuille_temps_tache).
     * Pivot simple sans attributs supplémentaires ni timestamps.
     */
    public function feuilleTemps(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            FeuilleTemps::class,
            'feuille_temps_tache', // table pivot
            'tache_id',            // FK de ce modèle dans le pivot
            'feuille_temps_id'     // FK du modèle lié dans le pivot
        );
    }
}

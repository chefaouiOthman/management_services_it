<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projets';

    protected $fillable = [
        'client_id',
        'nom_projet',
        'description',
        'budget_vendu',
        'statut_projet',
    ];

    protected $casts = [
        'budget_vendu' => 'decimal:2',
    ];

    // =========================================================
    // RELATIONS MODULE 1 : remontée vers le Client propriétaire
    // client_id FK → clients.user_id (PK identitaire)
    // =========================================================

    /** Client qui a commandé ce projet */
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 3 : PRODUCTION
    // =========================================================

    /** Livrables (jalons) associés à ce projet */
    public function livrables(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Livrable::class, 'projet_id');
    }

    /**
     * Tâches associées à ce projet via le pivot projet_tache.
     * withPivot() expose les attributs priorite et statut_tache.
     * withTimestamps() active created_at/updated_at sur le pivot.
     */
    public function taches(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Tache::class,
            'projet_tache',   // table pivot
            'projet_id',      // FK de ce modèle dans le pivot
            'tache_id'        // FK du modèle lié dans le pivot
        )
        ->withPivot(['priorite', 'statut_tache'])
        ->withTimestamps();
    }

    /** Feuilles de temps imputées sur ce projet */
    public function feuilleTemps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeuilleTemps::class, 'projet_id');
    }

    /**
     * Technologies utilisées dans ce projet via le pivot projet_technologie.
     * Pas d'attributs de pivot supplémentaires selon GEMINI.md.
     */
    public function technologies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Technologie::class,
            'projet_technologie', // table pivot
            'projet_id',          // FK de ce modèle dans le pivot
            'technologie_id'      // FK du modèle lié dans le pivot
        );
    }
}

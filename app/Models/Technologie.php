<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technologie extends Model
{
    use HasFactory;

    protected $table = 'technologies';

    protected $fillable = [
        'nom_tech',
        'version',
    ];

    protected $casts = [];

    // =========================================================
    // RELATIONS MODULE 3 (côté inverse)
    // =========================================================

    /**
     * Projets utilisant cette technologie (via pivot projet_technologie).
     * Pas d'attributs de pivot supplémentaires selon GEMINI.md.
     */
    public function projets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Projet::class,
            'projet_technologie', // table pivot
            'technologie_id',     // FK de ce modèle dans le pivot
            'projet_id'           // FK du modèle lié dans le pivot
        );
    }
}

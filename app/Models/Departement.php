<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    protected $table = 'departements';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'nom_departement',
    ];

    /**
     * Aucun cast spécifique requis pour Departement.
     */
    protected $casts = [];

    // =========================================================
    // NOTE ARCHITECTURALE :
    // Selon GEMINI.md, la table 'employes' ne possède PAS de colonne
    // 'departement_id'. La relation Departement → Employes est donc
    // supprimée car elle référencerait un champ inexistant en BDD.
    // Si cette relation est nécessaire fonctionnellement, il faudra
    // amender le GEMINI.md et ajouter la colonne dans une nouvelle migration.
    // =========================================================
}

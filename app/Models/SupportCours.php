<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportCours extends Model
{
    use HasFactory;

    protected $table = 'support_cours';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     * 'catalogue_formation_id' retiré : SupportCours est une entité standalone,
     * la liaison passe par le pivot catalogue_formation_support.
     */
    protected $fillable = [
        'nom_fichier',
        'url_stockage',
    ];

    protected $casts = [];

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS (côté inverse)
    // =========================================================

    /**
     * Catalogues de formation auxquels ce support est associé.
     * Relation N,N via pivot catalogue_formation_support (GEMINI.md pivot #5).
     */
    public function catalogueFormations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            CatalogueFormation::class,
            'catalogue_formation_support', // table pivot
            'support_cours_id',            // FK de ce modèle dans le pivot
            'catalogue_formation_id'       // FK du modèle lié dans le pivot
        );
    }
}

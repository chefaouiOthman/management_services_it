<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueFormation extends Model
{
    use HasFactory;

    protected $table = 'catalogue_formations';

    protected $fillable = [
        'titre_formation',
        'description_programme',
        'prix_standard',
    ];

    protected $casts = [
        'prix_standard' => 'decimal:2',
    ];

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // =========================================================

    /**
     * Supports de cours associés à ce catalogue (via pivot catalogue_formation_support).
     * Relation N,N conforme GEMINI.md pivot #5.
     */
    public function supportCours(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            SupportCours::class,
            'catalogue_formation_support', // table pivot
            'catalogue_formation_id',      // FK de ce modèle dans le pivot
            'support_cours_id'             // FK du modèle lié dans le pivot
        );
    }

    /** Sessions de formation basées sur ce catalogue */
    public function sessionFormations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SessionFormation::class, 'catalogue_formation_id');
    }
}

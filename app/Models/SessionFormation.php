<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionFormation extends Model
{
    use HasFactory;

    protected $table = 'session_formations';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     * 'employe_id' retiré : la liaison Formateur ↔ Session passe
     * par le pivot employe_session_formation (N,N).
     */
    protected $fillable = [
        'catalogue_formation_id',
        'date_debut',
        'date_fin',
        'salle_virtuelle',
        'salle_concrete',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // =========================================================

    /** Catalogue de formation sur lequel est basée cette session */
    public function catalogueFormation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CatalogueFormation::class, 'catalogue_formation_id');
    }

    /**
     * Formateurs (Employés) qui animent cette session.
     * Relation N,N via pivot employe_session_formation (GEMINI.md pivot #4).
     * employe_id FK → employes.user_id (PK identitaire).
     */
    public function formateurs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Employe::class,
            'employe_session_formation', // table pivot
            'session_formation_id',      // FK de ce modèle dans le pivot
            'employe_id'                 // FK du modèle lié dans le pivot
        );
    }

    /** Inscriptions des apprenants à cette session */
    public function inscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Inscription::class, 'session_formation_id');
    }

    /** Évaluations soumises pour cette session */
    public function evaluationSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationSession::class, 'session_formation_id');
    }

    /**
     * Alias pour compatibilité avec les vues qui utilisent $session->evaluations.
     * Délègue à evaluationSessions().
     */
    public function evaluations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationSession::class, 'session_formation_id');
    }
}

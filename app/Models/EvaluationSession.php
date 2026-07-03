<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSession extends Model
{
    use HasFactory;

    protected $table = 'evaluation_sessions';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     * employe_id = Formateur évalué (ajout manquant dans la version originale).
     */
    protected $fillable = [
        'session_formation_id',
        'user_id',      // Apprenant (Student)
        'employe_id',   // Formateur évalué (Trainer)
        'note_pedagogie',
        'note_technique',
        'avis_textuel', // Nullable selon GEMINI.md
    ];

    protected $casts = [
        'note_pedagogie' => 'integer',
        'note_technique' => 'integer',
    ];

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // =========================================================

    /** Session de formation concernée par cette évaluation */
    public function sessionFormation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SessionFormation::class, 'session_formation_id');
    }

    /** Apprenant (Student) qui a soumis cette évaluation */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formateur (Trainer) évalué par cet apprenant.
     * employe_id FK → employes.user_id (PK identitaire, ownerKey explicite).
     */
    public function formateur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Employe extends Model
{
    use HasFactory;

    protected $table = 'employes';

    /**
     * Clé primaire identitaire : user_id est la PK mais PAS auto-incrémentée.
     * Elle est partagée avec la table 'users' (One-to-One identitaire).
     */
    protected $primaryKey = 'user_id';
    public $incrementing  = false;
    protected $keyType    = 'int';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'user_id',
        'date_embauche',
        'departement_id',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    // =========================================================
    // RELATION PARENTE : remontée vers le User propriétaire
    // =========================================================

    /** Remonte vers le compte utilisateur (table users) */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'nom_complet' => 'Utilisateur inconnu',
            'name'        => 'Inconnu',
            'email'       => 'N/A',
        ]);
    }

    /** Relation vers le Departement */
    public function departement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Departement::class)->withDefault(['nom' => 'Département inconnu']);
    }

    // =========================================================
    // RELATIONS MODULE 2 : RH
    // Clé étrangère : employe_id sur la table enfant pointe vers user_id ici
    // =========================================================

    /** Contrats de travail de cet employé (1-N) */
    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class, 'employe_id', 'user_id');
    }

    /** Contrat actuel : actif le plus récent, sinon le dernier créé */
    public function getContratActuelAttribute(): ?Contrat
    {
        $contrats = $this->relationLoaded('contrats')
            ? $this->contrats
            : $this->contrats()->orderByDesc('id')->get();

        return $contrats->where('statut', 'actif')->sortByDesc('id')->first()
            ?? $contrats->sortByDesc('id')->first();
    }

    // =========================================================
    // RELATIONS MODULE 3 : PRODUCTION
    // =========================================================

    /** Feuilles de temps saisies par cet employé */
    public function feuilleTemps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeuilleTemps::class, 'employe_id', 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // Employe agit ici en tant que Formateur (Trainer)
    // =========================================================

    /** Sessions de formation animées par cet employé (rôle Formateur) */
    public function sessionsEnseignees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            SessionFormation::class,
            'employe_session_formation', // table pivot
            'employe_id',               // FK de ce modèle dans le pivot
            'session_formation_id'       // FK du modèle lié dans le pivot
        );
    }

    /** Évaluations reçues par cet employé en tant que formateur */
    public function evaluationsRecues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationSession::class, 'employe_id', 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 6 : FINANCE
    // =========================================================

    /** Fiches de paie de cet employé */
    public function fichePaies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FichePaie::class, 'employe_id', 'user_id');
    }

    /** Notes de frais soumises par cet employé */
    public function noteDeFrais(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NoteDeFrais::class, 'employe_id', 'user_id');
    }

    public function scopeVisiblePourAdmin($query)
    {
        if (!auth()->hasUser() || !auth()->user()->hasRole('Super Admin')) {
            $query->whereHas('user', fn ($q) => $q->whereDoesntHave('roles', fn ($r) => $r->where('name', 'Super Admin')));
        }
    }
}

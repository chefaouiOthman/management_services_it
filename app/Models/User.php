<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     * Note : 'password' est géré séparément via le cast 'hashed'.
     */
    protected $fillable = [
        'email',
        'password',
        'nom_complet',
        'est_actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts : boolean pour est_actif, hashed pour password.
     * email_verified_at conservé pour compatibilité Breeze.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'est_actif'         => 'boolean',
        'password'          => 'hashed',
    ];

    // =========================================================
    // RELATIONS D'HÉRITAGE IDENTITAIRE (One-to-One, PK partagée)
    // hasOne avec clé étrangère 'user_id' sur la table enfant
    // =========================================================

    /** Profil Employé (héritage identitaire One-to-One) */
    public function employe(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employe::class, 'user_id');
    }

    /** Profil Stagiaire (héritage identitaire One-to-One) */
    public function stagiaire(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Stagiaire::class, 'user_id');
    }

    /** Profil Client (héritage identitaire One-to-One) */
    public function client(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 2 : RH & SÉCURITÉ
    // =========================================================

    /** Pointages de présence enregistrés pour cet utilisateur */
    public function pointages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pointage::class, 'user_id');
    }

    /** Historique des tentatives de passage en zones sécurisées */
    public function historiquePassages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistoriquePassage::class, 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 4 : FORMATIONS
    // =========================================================

    /** Inscriptions aux sessions de formation */
    public function inscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Inscription::class, 'user_id');
    }

    /** Évaluations soumises par cet utilisateur (en tant qu'apprenant) */
    public function evaluationsSession(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationSession::class, 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 5 : ACTIFS IT
    // =========================================================

    /** Matériels assignés à cet utilisateur (pivot assignation_materiels) */
    public function assignationsMateriel(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssignationMateriel::class, 'user_id');
    }

    /** Licences assignées à cet utilisateur (pivot assignation_licences) */
    public function assignationsLicence(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssignationLicence::class, 'user_id');
    }

    /** Tickets de maintenance soumis par cet utilisateur */
    public function ticketsMaintenance(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketMaintenance::class, 'user_id');
    }
}

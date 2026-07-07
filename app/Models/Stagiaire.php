<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    use HasFactory;

    protected $table = 'stagiaires';

    /**
     * Clé primaire identitaire : user_id est la PK mais PAS auto-incrémentée.
     * Elle est partagée avec la table 'users' (One-to-One identitaire).
     */
    protected $primaryKey = 'user_id';
    public $incrementing  = false;
    protected $keyType    = 'int';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     * 'employe_id' (tuteur) a été supprimé car absent du dictionnaire de données.
     */
    protected $fillable = [
        'user_id',
        'ecole_origine',
        'sujet_stage',
        'departement_id',
    ];

    /**
     * Aucun cast date/boolean requis pour Stagiaire selon GEMINI.md.
     * (les champs sont tous de type string/text)
     */
    protected $casts = [];

    // =========================================================
    // RELATION PARENTE : remontée vers le User propriétaire
    // =========================================================

    /** Remonte vers le compte utilisateur (table users) */
    /** Remonte vers le compte utilisateur (table users) */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Relation vers le Departement */
    public function departement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }
}

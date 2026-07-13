<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

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
        'type_client',
        'nom_societe',
        'ice',
    ];

    /**
     * Casts : type_client est un enum, nom_societe et ice sont nullable strings.
     */
    protected $casts = [
        'type_client' => 'string',
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

    // =========================================================
    // RELATIONS MODULE 3 : PRODUCTION
    // La FK 'client_id' sur projets/factures pointe vers user_id ici
    // =========================================================

    /** Projets commandés par ce client */
    public function projets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Projet::class, 'client_id', 'user_id');
    }

    // =========================================================
    // RELATIONS MODULE 6 : FINANCE
    // =========================================================

    /** Factures émises pour ce client */
    public function factures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Facture::class, 'client_id', 'user_id');
    }
}

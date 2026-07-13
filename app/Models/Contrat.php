<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    protected $table = 'contrats';

    /**
     * Champs autorisés en mass-assignment (conformes GEMINI.md).
     */
    protected $fillable = [
        'employe_id',
        'type_contrat',
        'date_debut',
        'date_fin',
        'salaire_base',
        'heures_hebdo',
        'statut',
    ];

    /**
     * Casts : dates, décimal et entier pour une hydratation PHP correcte.
     */
    protected $casts = [
        'date_debut'   => 'date',
        'date_fin'     => 'date',
        'salaire_base' => 'decimal:2',
        'heures_hebdo' => 'integer',
    ];

    // =========================================================
    // RELATIONS MODULE 2 : RH
    // =========================================================

    /**
     * Remonte vers l'Employé propriétaire de ce contrat.
     *
     * La FK 'employe_id' dans 'contrats' référence 'user_id' dans 'employes'.
     * Eloquent doit donc connaître les deux clés :
     *   - foreignKey  = 'employe_id'  (colonne dans la table courante 'contrats')
     *   - ownerKey    = 'user_id'     (colonne PK dans la table 'employes')
     */
    public function employe(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id', 'user_id')->withDefault();
    }
}

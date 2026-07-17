<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeuilleTemps extends Model
{
    use HasFactory;

    protected $table = 'feuille_temps';

    protected $fillable = [
        'employe_id',
        'projet_id',
        'date_effort',
        'duree_heures',
        'commentaire',
        'created_by',
    ];

    protected $casts = [
        'date_effort'  => 'date',
        'duree_heures' => 'decimal:2',
    ];

    // =========================================================
    // RELATIONS MODULE 3
    // =========================================================

    /**
     * Employé qui a saisi cette feuille de temps.
     * employe_id FK → employes.user_id (PK identitaire, ownerKey explicite).
     */
    public function employe(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id', 'user_id')->withDefault();
    }

    /** Projet sur lequel est imputé cet effort */
    public function projet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Projet::class, 'projet_id')->withDefault();
    }

    /**
     * Tâches rattachées à cette feuille de temps (via pivot feuille_temps_tache).
     * Pivot simple sans attributs supplémentaires ni timestamps.
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'nom_complet' => 'Système',
            'name'        => 'Système',
        ]);
    }

    public function taches(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Tache::class,
            'feuille_temps_tache', // table pivot
            'feuille_temps_id',    // FK de ce modèle dans le pivot
            'tache_id'             // FK du modèle lié dans le pivot
        );
    }
}

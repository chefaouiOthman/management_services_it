<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMateriel extends Model
{
    use HasFactory;

    // Nom de la table dans la base de données
    protected $table = 'asset_materiels';

    // Liste des champs autorisés à l'écriture (Mass Assignment)
    protected $fillable = [
        'type_materiel_id',
        'num_serie',
        'marque',           // Ajouté pour permettre au contrôleur de l'enregistrer
        'modele',           // Ajouté pour permettre au contrôleur de l'enregistrer
        'date_achat_actif',
        'statut_materiel',  // Conservé tel quel selon ton choix
        'prix_achat',
    ];

    // Conversion automatique des types de données
    protected $casts = [
        'date_achat_actif' => 'date',
        'prix_achat'       => 'decimal:2',
    ];

    /**
     * RELATION : Un matériel appartient à un seul type (ex: PC, Écran)
     */
    public function typeMateriel()
    {
        return $this->belongsTo(TypeMateriel::class, 'type_materiel_id');
    }

    /**
     * RELATION MANQUANTE AJOUTÉE : Un matériel peut être assigné à des utilisateurs
     * Fait le lien avec la table pivot 'assignation_materiels' (conforme GEMINI.md)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'assignation_materiels', 'asset_materiel_id', 'user_id')
            ->withPivot(['id', 'date_remise', 'date_restitution'])
            ->withTimestamps();
    }

    /**
     * RELATION : Historique des assignations (si tu l'utilises par ailleurs)
     */
    public function assignations()
    {
        return $this->hasMany(AssignationMateriel::class, 'asset_materiel_id');
    }

    /**
     * RELATION : Les tickets de maintenance liés à ce matériel
     */
    public function ticketsMaintenance()
    {
        return $this->hasMany(TicketMaintenance::class, 'asset_materiel_id');
    }
}

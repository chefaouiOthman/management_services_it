<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMaintenance extends Model
{
    use HasFactory;

    protected $table = 'ticket_maintenances';

    protected $fillable = [
        'asset_materiel_id',
        'user_id',
        'description_panne',
        'cout_reparation',
        'statut_ticket',
    ];

    protected $casts = [
        'cout_reparation' => 'decimal:2',
    ];

    public function assetMateriel()
    {
        return $this->belongsTo(AssetMateriel::class, 'asset_materiel_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'nom_complet' => 'Utilisateur inconnu',
            'name'        => 'Inconnu',
            'email'       => 'N/A',
        ]);
    }
}

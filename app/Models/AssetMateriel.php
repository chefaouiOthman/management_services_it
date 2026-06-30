<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMateriel extends Model
{
    use HasFactory;

    protected $table = 'asset_materiels';

    protected $fillable = [
        'type_materiel_id',
        'num_serie',
        'date_achat_actif',
        'statut_materiel',
        'prix_achat',
    ];

    protected $casts = [
        'date_achat_actif' => 'date',
        'prix_achat' => 'decimal:2',
    ];

    public function typeMateriel()
    {
        return $this->belongsTo(TypeMateriel::class, 'type_materiel_id');
    }

    public function assignations()
    {
        return $this->hasMany(AssignationMateriel::class, 'asset_materiel_id');
    }

    public function ticketsMaintenance()
    {
        return $this->hasMany(TicketMaintenance::class, 'asset_materiel_id');
    }
}

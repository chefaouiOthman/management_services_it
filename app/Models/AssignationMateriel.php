<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignationMateriel extends Model
{
    use HasFactory;

    protected $table = 'assignation_materiels';

    protected $fillable = [
        'user_id',
        'asset_materiel_id',
        'date_remise',
        'date_restitution',
    ];

    protected $casts = [
        'date_remise' => 'date',
        'date_restitution' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'nom_complet' => 'Utilisateur inconnu',
            'name'        => 'Inconnu',
            'email'       => 'N/A',
        ]);
    }

    public function assetMateriel()
    {
        return $this->belongsTo(AssetMateriel::class, 'asset_materiel_id')->withDefault();
    }
}

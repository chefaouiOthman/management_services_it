<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMateriel extends Model
{
    use HasFactory;

    protected $table = 'type_materiels';

    protected $fillable = [
        'libelle_type',
        'description_type',
    ];

    public function assetMateriels()
    {
        return $this->hasMany(AssetMateriel::class, 'type_materiel_id');
    }
}

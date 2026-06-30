<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieFlux extends Model
{
    use HasFactory;

    protected $table = 'categorie_flux';

    protected $fillable = [
        'libelle_categorie',
        'code_comptable',
    ];

    public function fluxTresoreries()
    {
        return $this->hasMany(FluxTresorerie::class, 'categorie_flux_id');
    }
}

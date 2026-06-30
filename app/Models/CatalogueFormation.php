<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueFormation extends Model
{
    use HasFactory;

    protected $table = 'catalogue_formations';

    protected $fillable = [
        'titre_formation',
        'description_programme',
        'prix_standard',
    ];

    protected $casts = [
        'prix_standard' => 'decimal:2',
    ];

    public function supportCours()
    {
        return $this->hasMany(SupportCours::class, 'catalogue_formation_id');
    }

    public function sessionFormations()
    {
        return $this->hasMany(SessionFormation::class, 'catalogue_formation_id');
    }
}

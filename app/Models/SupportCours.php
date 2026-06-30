<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportCours extends Model
{
    use HasFactory;

    protected $table = 'support_cours';

    protected $fillable = [
        'catalogue_formation_id',
        'nom_fichier',
        'url_stockage',
    ];

    public function catalogueFormation()
    {
        return $this->belongsTo(CatalogueFormation::class, 'catalogue_formation_id');
    }
}

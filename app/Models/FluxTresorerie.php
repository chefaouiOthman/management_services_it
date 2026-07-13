<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxTresorerie extends Model
{
    use HasFactory;

    protected $table = 'flux_tresoreries';

    protected $fillable = [
        'categorie_flux_id',
        'type_mouvement',
        'montant_operation',
        'date_comptable',
    ];

    protected $casts = [
        'montant_operation' => 'decimal:2',
        'date_comptable' => 'datetime',
    ];

    public function categorieFlux()
    {
        return $this->belongsTo(CategorieFlux::class, 'categorie_flux_id')->withDefault();
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'flux_tresorerie_id');
    }

    public function fichePaie()
    {
        return $this->hasOne(FichePaie::class, 'flux_tresorerie_id');
    }

    public function noteDeFrais()
    {
        return $this->hasOne(NoteDeFrais::class, 'flux_tresorerie_id');
    }
}

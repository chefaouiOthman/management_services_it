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
        'facture_id',
        'fiche_paie_id',
        'note_de_frais_id',
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
        return $this->belongsTo(CategorieFlux::class, 'categorie_flux_id');
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class, 'facture_id');
    }

    public function fichePaie()
    {
        return $this->belongsTo(FichePaie::class, 'fiche_paie_id');
    }

    public function noteDeFrais()
    {
        return $this->belongsTo(NoteDeFrais::class, 'note_de_frais_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFacture extends Model
{
    use HasFactory;

    protected $table = 'ligne_factures';

    protected $fillable = [
        'facture_id',
        'designation',
        'quantite',
        'prix_unitaire_ht',
        'taux_tva',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire_ht' => 'decimal:2',
        'taux_tva' => 'decimal:2',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class, 'facture_id')->withDefault();
    }
}

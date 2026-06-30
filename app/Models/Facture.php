<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $table = 'factures';

    protected $fillable = [
        'client_id',
        'num_facture',
        'date_emission',
        'statut_paiement',
    ];

    protected $casts = [
        'date_emission' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function ligneFactures()
    {
        return $this->hasMany(LigneFacture::class, 'facture_id');
    }

    public function fluxTresorerie()
    {
        return $this->hasOne(FluxTresorerie::class, 'facture_id');
    }
}

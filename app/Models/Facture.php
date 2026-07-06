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
        'flux_tresorerie_id',
        'num_facture',
        'date_emission',
        'statut_paiement',
    ];

    protected $casts = [
        'date_emission' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'user_id');
    }

    public function ligneFactures()
    {
        return $this->hasMany(LigneFacture::class, 'facture_id');
    }

    public function fluxTresorerie()
    {
        return $this->belongsTo(FluxTresorerie::class, 'flux_tresorerie_id');
    }

    /**
     * Accesseur calculant le Total TTC de la facture dynamiquement.
     */
    public function getTotalTtcAttribute()
    {
        return $this->ligneFactures->sum(function ($ligne) {
            $ht = $ligne->quantite * $ligne->prix_unitaire_ht;
            $tva = $ht * ($ligne->taux_tva / 100);
            return $ht + $tva;
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichePaie extends Model
{
    use HasFactory;

    protected $table = 'fiche_paies';

    protected $fillable = [
        'employe_id',
        'flux_tresorerie_id',
        'mois_annee',
        'net_a_payer',
    ];

    protected $casts = [
        'net_a_payer' => 'decimal:2',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id', 'user_id');
    }

    public function fluxTresorerie()
    {
        return $this->belongsTo(FluxTresorerie::class, 'flux_tresorerie_id');
    }
}

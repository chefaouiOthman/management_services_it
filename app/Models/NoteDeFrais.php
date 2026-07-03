<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteDeFrais extends Model
{
    use HasFactory;

    protected $table = 'note_de_frais';

    protected $fillable = [
        'employe_id',
        'flux_tresorerie_id',
        'motif_depense',
        'montant_ttc',
        'justificatif_path',
        'statut_remboursement',
    ];

    protected $casts = [
        'montant_ttc' => 'decimal:2',
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

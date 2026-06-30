<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    protected $table = 'contrats';

    protected $fillable = [
        'employe_id',
        'type_contrat',
        'date_debut',
        'date_fin',
        'salaire_base',
        'heures_hebdo',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'salaire_base' => 'decimal:2',
        'heures_hebdo' => 'integer',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }
}

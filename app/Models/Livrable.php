<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livrable extends Model
{
    use HasFactory;

    protected $table = 'livrables';

    protected $fillable = [
        'projet_id',
        'titre_jalon',
        'date_limite_soumission',
        'statut_client',
    ];

    protected $casts = [
        'date_limite_soumission' => 'date',
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }
}

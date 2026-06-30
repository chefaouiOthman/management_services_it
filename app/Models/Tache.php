<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $table = 'taches';

    protected $fillable = [
        'titre_tache',
    ];

    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_tache', 'tache_id', 'projet_id')
            ->withPivot(['statut_tache', 'priorite'])
            ->withTimestamps();
    }

    public function feuilleTemps()
    {
        return $this->belongsToMany(FeuilleTemps::class, 'feuille_temps_tache', 'tache_id', 'feuille_temps_id')->withTimestamps();
    }
}

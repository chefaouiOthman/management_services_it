<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeuilleTemps extends Model
{
    use HasFactory;

    protected $table = 'feuille_temps';

    protected $fillable = [
        'employe_id',
        'projet_id',
        'date_effort',
        'duree_heures',
        'commentaire',
    ];

    protected $casts = [
        'date_effort' => 'date',
        'duree_heures' => 'decimal:2',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    public function taches()
    {
        return $this->belongsToMany(Tache::class, 'feuille_temps_tache', 'feuille_temps_id', 'tache_id')->withTimestamps();
    }
}

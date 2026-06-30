<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projets';

    protected $fillable = [
        'client_id',
        'nom_projet',
        'description',
        'budget_vendu',
        'statut_projet',
    ];

    protected $casts = [
        'budget_vendu' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function livrables()
    {
        return $this->hasMany(Livrable::class, 'projet_id');
    }

    public function taches()
    {
        return $this->belongsToMany(Tache::class, 'projet_tache', 'projet_id', 'tache_id')
            ->withPivot(['statut_tache', 'priorite'])
            ->withTimestamps();
    }

    public function feuilleTemps()
    {
        return $this->hasMany(FeuilleTemps::class, 'projet_id');
    }

    public function technologies()
    {
        return $this->belongsToMany(Technologie::class, 'projet_technologie', 'projet_id', 'technologie_id')->withTimestamps();
    }
}

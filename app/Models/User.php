<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    protected $fillable = [
        'email',
        'password',
        'nom_complet',
        'est_actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'password' => 'hashed',
    ];


    public function employe()
    {
        return $this->hasOne(Employe::class, 'user_id');
    }

    public function stagiaire()
    {
        return $this->hasOne(Stagiaire::class, 'user_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class, 'user_id');
    }

    public function zonesAccessible()
    {
        return $this->belongsToMany(Zone::class, 'user_zone', 'user_id', 'zone_id')->withTimestamps();
    }

    public function historiquePassages()
    {
        return $this->hasMany(HistoriquePassage::class, 'user_id');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'user_id');
    }

    public function evaluationsSession()
    {
        return $this->hasMany(EvaluationSession::class, 'user_id');
    }

    public function assignationsMateriel()
    {
        return $this->hasMany(AssignationMateriel::class, 'user_id');
    }
}

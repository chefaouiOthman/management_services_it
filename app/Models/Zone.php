<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';

    protected $fillable = [
        'code_zone',
        'nom_salle',
        'niveau_requis',
        'est_active',
    ];

    protected $casts = [
        'niveau_requis' => 'integer',
        'est_active' => 'boolean',
    ];

    public function usersWithAccess()
    {
        return $this->belongsToMany(User::class, 'user_zone', 'zone_id', 'user_id')->withTimestamps();
    }

    public function historiquePassages()
    {
        return $this->hasMany(HistoriquePassage::class, 'zone_id');
    }
}

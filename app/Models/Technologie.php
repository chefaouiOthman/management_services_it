<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technologie extends Model
{
    use HasFactory;

    protected $table = 'technologies';

    protected $fillable = [
        'nom_tech',
        'version',
    ];

    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_technologie', 'technologie_id', 'projet_id')->withTimestamps();
    }
}

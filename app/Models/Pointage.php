<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;

    protected $table = 'pointages';

    protected $fillable = [
        'user_id',
        'date_jour',
        'heure_arrivee',
        'heure_depart',
        'statut_presence',
    ];

    protected $casts = [
        'date_jour' => 'date',
        'heure_arrivee' => 'datetime',
        'heure_depart' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

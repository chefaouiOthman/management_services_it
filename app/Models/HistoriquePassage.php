<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePassage extends Model
{
    use HasFactory;

    protected $table = 'historique_passages';

    protected $fillable = [
        'user_id',
        'zone_id',
        'horodatage',
        'tentative_statut',
    ];

    protected $casts = [
        'horodatage' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionFormation extends Model
{
    use HasFactory;

    protected $table = 'session_formations';

    protected $fillable = [
        'catalogue_formation_id',
        'employe_id',
        'date_debut',
        'date_fin',
        'salle_virtuelle',
        'salle_concrete',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function catalogueFormation()
    {
        return $this->belongsTo(CatalogueFormation::class, 'catalogue_formation_id');
    }

    public function formateur()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'session_formation_id');
    }

    public function evaluationSessions()
    {
        return $this->hasMany(EvaluationSession::class, 'session_formation_id');
    }
}

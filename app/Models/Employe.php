<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    use HasFactory;

    protected $table = 'employes';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'date_embauche',
        'CIN',
        'departement_id',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'employe_id', 'user_id');
    }

    public function stagiairesEncadres()
    {
        return $this->hasMany(Stagiaire::class, 'employe_id', 'user_id');
    }

    public function feuilleTemps()
    {
        return $this->hasMany(FeuilleTemps::class, 'employe_id', 'user_id');
    }

    public function sessionsEnseignees()
    {
        return $this->belongsToMany(SessionFormation::class, 'employe_session_formation', 'employe_id', 'session_formation_id');
    }

    public function ticketsMaintenanceSignales()
    {
        return $this->hasMany(TicketMaintenance::class, 'user_id', 'user_id');
    }

    public function assignationsLicences()
    {
        return $this->hasMany(AssignationLicence::class, 'user_id', 'user_id');
    }

    public function fichePaies()
    {
        return $this->hasMany(FichePaie::class, 'employe_id', 'user_id');
    }

    public function noteDeFrais()
    {
        return $this->hasMany(NoteDeFrais::class, 'employe_id', 'user_id');
    }
}

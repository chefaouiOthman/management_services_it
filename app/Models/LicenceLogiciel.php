<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceLogiciel extends Model
{
    use HasFactory;

    protected $table = 'licence_logiciels';

    protected $fillable = [
        'nom_logiciel',
        'cle_licence',
        'date_expiration',
    ];

    protected $casts = [
        'date_expiration' => 'date',
    ];

    public function assignations()
    {
        return $this->hasMany(AssignationLicence::class, 'licence_logiciel_id');
    }
}

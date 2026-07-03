<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignationLicence extends Model
{
    use HasFactory;

    protected $table = 'assignation_licences';

    protected $fillable = [
        'user_id',
        'licence_logiciel_id',
        'date_attribution',
        'date_revocation',
    ];

    protected $casts = [
        'date_attribution' => 'date',
        'date_revocation' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function licenceLogiciel()
    {
        return $this->belongsTo(LicenceLogiciel::class, 'licence_logiciel_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    use HasFactory;

    protected $table = 'stagiaires';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'ecole_origine',
        'sujet_stage',
        'employe_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function encadrant()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }
}

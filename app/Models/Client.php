<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'type_client',
        'nom_societe',
        'ice',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function projets()
    {
        return $this->hasMany(Projet::class, 'client_id');
    }

    public function factures()
    {
        return $this->hasMany(Facture::class, 'client_id');
    }
}

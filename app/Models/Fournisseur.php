<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom', 'email', 'telephone', 'adresse'
    ];

    // Un fournisseur a plusieurs approvisionnements
    public function aprovisionnements()
    {
        return $this->hasMany(Aprovisionnement::class);
    }
}

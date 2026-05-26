<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitVenteStats extends Model
{
    protected $fillable = [
        'product_id',
        'saison',
        'rang',
        'total_quantite_vendue',
        'total_chiffre_affaires',
        'nombre_ventes',
        'derniere_analyse',
    ];

    protected $casts = [
        'derniere_analyse' => 'datetime',
        'total_chiffre_affaires' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

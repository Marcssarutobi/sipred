<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AprovisionnementItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'aprovisionnement_id', 'product_id', 'quantite',
        'prix_unitaire', 'prix_total'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'prix_total'    => 'decimal:2',
    ];

    // Appartient à un approvisionnement
    public function aprovisionnement()
    {
        return $this->belongsTo(Aprovisionnement::class);
    }

    // Appartient à un produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

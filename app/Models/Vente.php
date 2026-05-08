<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'reference', 'montant_total', 'montant_paye',
        'monnaie', 'status', 'date', 'user_id'
    ];

    protected $casts = [
        'date'          => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye'  => 'decimal:2',
        'monnaie'       => 'decimal:2',
    ];

    // Effectuée par un user (magasinier)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une vente a plusieurs lignes
    public function items()
    {
        return $this->hasMany(VenteItem::class);
    }

    // Les produits vendus (via la table pivot vente_items)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'vente_items')
                    ->withPivot('quantite', 'prix_unitaire', 'prix_total');
    }

    // Helper : vente soldée
    public function estPayee(): bool
    {
        return $this->status === 'paye';
    }
}

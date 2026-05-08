<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprovisionnement extends Model
{
    protected $fillable = [
        'reference', 'fournisseur_id', 'montant_total',
        'date_approvisionnement', 'user_id','status'
    ];

    protected $casts = [
        'date_approvisionnement' => 'datetime',
        'montant_total'          => 'decimal:2',
    ];

    // Appartient à un fournisseur
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    // Créé par un user (gérant)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A plusieurs lignes
    public function items()
    {
        return $this->hasMany(AprovisionnementItem::class);
    }

    // Les produits approvisionnés
    public function products()
    {
        return $this->belongsToMany(Product::class, 'aprovisionnement_items')
                    ->withPivot('quantite', 'prix_unitaire', 'prix_total');
    }
}

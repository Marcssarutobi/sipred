<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code_barre', 'nom', 'prix_unitaire', 'prix_achat',
        'quantite', 'seuil_alerte', 'date_expiration',
        'image', 'category_id', 'user_id','fournisseur_id'
    ];

    protected $casts = [
        'date_expiration' => 'date',
        'prix_unitaire'   => 'decimal:2',
        'prix_achat'      => 'decimal:2',
    ];

    // Appartient à une catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Créé par un user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un produit a plusieurs mouvements de stock
    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    // Un produit apparaît dans plusieurs lignes de vente
    public function venteItems()
    {
        return $this->hasMany(VenteItem::class);
    }

    // Un produit apparaît dans plusieurs lignes d'approvisionnement
    public function aprovisionnementItems()
    {
        return $this->hasMany(AprovisionnementItem::class);
    }

    // Helper : stock en alerte
    public function enAlerte(): bool
    {
        return $this->quantite <= $this->seuil_alerte;
    }

    // Helper : stock épuisé
    public function enRupture(): bool
    {
        return $this->quantite === 0;
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
}

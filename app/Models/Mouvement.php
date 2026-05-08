<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = [
        'product_id', 'quantite', 'type', 'date', 'motif', 'user_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Appartient à un produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Enregistré par un user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

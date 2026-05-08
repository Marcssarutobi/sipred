<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Un user peut créer plusieurs produits
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Un user peut effectuer plusieurs ventes
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    // Un user peut enregistrer plusieurs mouvements
    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    // Un user peut créer plusieurs approvisionnements
    public function aprovisionnements()
    {
        return $this->hasMany(Aprovisionnement::class);
    }

    // Helpers pour vérifier le rôle
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGerant(): bool
    {
        return $this->role === 'gerant';
    }

    public function isMagasinier(): bool
    {
        return $this->role === 'magasinier';
    }
}

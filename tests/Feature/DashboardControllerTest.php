<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProduitVenteStats;
use App\Models\User;
use App\Models\Vente;
use App\Models\VenteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_summary_and_chart_data(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $category = Category::create([
            'name' => 'Boissons',
        ]);

        $product = Product::create([
            'code_barre' => '1234567890123',
            'nom' => 'Jus Orange',
            'prix_unitaire' => 1200,
            'prix_achat' => 800,
            'quantite' => 4,
            'seuil_alerte' => 5,
            'date_expiration' => null,
            'image' => null,
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $vente = Vente::create([
            'reference' => 'VNT-TEST01',
            'montant_total' => 2400,
            'montant_paye' => 2400,
            'monnaie' => 0,
            'status' => 'paye',
            'date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        VenteItem::create([
            'vente_id' => $vente->id,
            'product_id' => $product->id,
            'quantite' => 2,
            'prix_unitaire' => 1200,
            'prix_total' => 2400,
        ]);

        ProduitVenteStats::create([
            'product_id' => $product->id,
            'saison' => 'Printemps',
            'rang' => 1,
            'total_quantite_vendue' => 2,
            'total_chiffre_affaires' => 2400,
            'nombre_ventes' => 1,
            'derniere_analyse' => now(),
        ]);

        $response = $this->getJson('/api/dashboard?saison=Printemps');

        $response
            ->assertOk()
            ->assertJsonPath('summary.products_count', 1)
            ->assertJsonPath('summary.low_stock_count', 1)
            ->assertJsonPath('produit_vente_stats.source', 'produit_vente_stats')
            ->assertJsonPath('produit_vente_stats.saison', 'Printemps')
            ->assertJsonPath('produit_vente_stats.items.0.produit_nom', 'Jus Orange')
            ->assertJsonPath('produit_vente_stats.items.0.total_quantite_vendue', 2)
            ->assertJsonPath('recent_sales.0.reference', 'VNT-TEST01');
    }
}

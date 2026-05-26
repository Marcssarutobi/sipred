<?php

namespace App\Http\Controllers;

use App\Models\Aprovisionnement;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vente;
use App\Services\ProduitVenteStatsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProduitVenteStatsService $produitVenteStatsService
    ) {
    }

    public function index(Request $request)
    {
        $produitVenteStats = $this->produitVenteStatsService->getSeasonSummary(
            $request->input('saison'),
            (int) $request->input('limit', 10),
        );

        $summary = [
            'products_count' => Product::count(),
            'categories_count' => Category::count(),
            'sales_count' => Vente::count(),
            'revenue' => (float) Vente::sum('montant_total'),
            'paid_sales_count' => Vente::where('status', 'paye')->count(),
            'unpaid_sales_count' => Vente::where('status', 'non_paye')->count(),
            'low_stock_count' => Product::whereColumn('quantite', '<=', 'seuil_alerte')->count(),
            'pending_supplies_count' => Aprovisionnement::where('status', 'enAttente')->count(),
        ];

        $recentSales = Vente::query()
            ->with('user:id,fullname')
            ->select('id', 'reference', 'montant_total', 'status', 'date', 'user_id')
            ->latest('date')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(function (Vente $vente) {
                return [
                    'id' => $vente->id,
                    'reference' => $vente->reference,
                    'montant_total' => (float) $vente->montant_total,
                    'status' => $vente->status,
                    'date' => $vente->date?->toDateString(),
                    'user' => $vente->user ? [
                        'id' => $vente->user->id,
                        'fullname' => $vente->user->fullname,
                    ] : null,
                ];
            })
            ->values();

        $lowStockProducts = Product::query()
            ->with('category:id,name')
            ->select('id', 'nom', 'quantite', 'seuil_alerte', 'category_id')
            ->whereColumn('quantite', '<=', 'seuil_alerte')
            ->orderBy('quantite')
            ->orderBy('nom')
            ->limit(6)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'nom' => $product->nom,
                    'quantite' => (int) $product->quantite,
                    'seuil_alerte' => (int) $product->seuil_alerte,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ] : null,
                ];
            })
            ->values();

        return response()->json([
            'summary' => $summary,
            'produit_vente_stats' => $produitVenteStats,
            'recent_sales' => $recentSales,
            'low_stock_products' => $lowStockProducts,
        ]);
    }
}

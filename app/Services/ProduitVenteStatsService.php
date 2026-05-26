<?php

namespace App\Services;

use App\Models\ProduitVenteStats;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProduitVenteStatsService
{
    public function getSeasonSummary(?string $season = null, int $limit = 10): array
    {
        $resolvedSeason = $this->resolveSeason($season);
        $resolvedLimit = max(1, min($limit, 10));

        $storedStats = ProduitVenteStats::query()
            ->with('product:id,nom,quantite,seuil_alerte')
            ->whereIn('saison', $this->seasonAliases($resolvedSeason))
            ->orderBy('rang')
            ->orderByDesc('total_quantite_vendue')
            ->limit($resolvedLimit)
            ->get();

        if ($storedStats->isNotEmpty()) {
            $items = $storedStats
                ->values()
                ->map(function (ProduitVenteStats $stat, int $index) {
                    return [
                        'product_id' => (int) $stat->product_id,
                        'produit_nom' => $stat->product?->nom ?? ('Produit #' . $stat->product_id),
                        'rang' => (int) ($stat->rang ?: $index + 1),
                        'total_quantite_vendue' => (int) $stat->total_quantite_vendue,
                        'total_chiffre_affaires' => (float) $stat->total_chiffre_affaires,
                        'nombre_ventes' => (int) $stat->nombre_ventes,
                        'stock_actuel' => (int) ($stat->product?->quantite ?? 0),
                        'seuil_alerte' => (int) ($stat->product?->seuil_alerte ?? 0),
                    ];
                });

            return $this->formatPayload($items, 'produit_vente_stats', $resolvedSeason);
        }

        $fallbackStats = DB::table('vente_items')
            ->join('ventes', 'vente_items.vente_id', '=', 'ventes.id')
            ->join('products', 'vente_items.product_id', '=', 'products.id')
            ->where('ventes.status', 'paye')
            ->where(function ($query) use ($resolvedSeason) {
                foreach ($this->seasonMonths($resolvedSeason) as $month) {
                    $query->orWhereMonth('ventes.date', $month);
                }
            })
            ->select(
                'products.id as product_id',
                'products.nom as produit_nom',
                'products.quantite as stock_actuel',
                'products.seuil_alerte',
                DB::raw('SUM(vente_items.quantite) as total_quantite_vendue'),
                DB::raw('SUM(vente_items.prix_total) as total_chiffre_affaires'),
                DB::raw('COUNT(DISTINCT ventes.id) as nombre_ventes')
            )
            ->groupBy('products.id', 'products.nom', 'products.quantite', 'products.seuil_alerte')
            ->orderByDesc('total_quantite_vendue')
            ->limit($resolvedLimit)
            ->get()
            ->values()
            ->map(function ($row, int $index) {
                return [
                    'product_id' => (int) $row->product_id,
                    'produit_nom' => $row->produit_nom,
                    'rang' => $index + 1,
                    'total_quantite_vendue' => (int) $row->total_quantite_vendue,
                    'total_chiffre_affaires' => (float) $row->total_chiffre_affaires,
                    'nombre_ventes' => (int) $row->nombre_ventes,
                    'stock_actuel' => (int) $row->stock_actuel,
                    'seuil_alerte' => (int) $row->seuil_alerte,
                ];
            });

        return $this->formatPayload($fallbackStats, 'live_fallback', $resolvedSeason);
    }

    public function currentSeason(?Carbon $date = null): string
    {
        $month = ($date ?? now())->month;

        return match (true) {
            $month >= 3 && $month <= 5 => 'Printemps',
            $month >= 6 && $month <= 8 => 'Été',
            $month >= 9 && $month <= 11 => 'Automne',
            default => 'Hiver',
        };
    }

    private function formatPayload(Collection $items, string $source, string $season): array
    {
        return [
            'source' => $source,
            'saison' => $season,
            'categories' => $items->pluck('produit_nom')->all(),
            'series' => [[
                'name' => 'Quantite vendue',
                'data' => $items->pluck('total_quantite_vendue')->map(fn ($value) => (int) $value)->all(),
            ]],
            'items' => $items->values()->all(),
        ];
    }

    private function resolveSeason(?string $season): string
    {
        if (in_array($season, ['Ete', 'Été'], true)) {
            return 'Été';
        }

        return in_array($season, ['Printemps', 'Automne', 'Hiver'], true)
            ? $season
            : $this->currentSeason();
    }

    private function seasonMonths(string $season): array
    {
        return match ($season) {
            'Printemps' => [3, 4, 5],
            'Été' => [6, 7, 8],
            'Automne' => [9, 10, 11],
            default => [12, 1, 2],
        };
    }

    private function seasonAliases(string $season): array
    {
        return $season === 'Été'
            ? ['Été', 'Ete']
            : [$season];
    }
}

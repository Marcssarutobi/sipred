<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\ProduitVenteStats;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AnalyseProduitsPlusVendusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $saisons = [
            'Printemps' => [3, 4, 5],
            'Été'       => [6, 7, 8],
            'Automne'   => [9, 10, 11],
            'Hiver'     => [12, 1, 2],
        ];

        foreach ($saisons as $nomSaison => $mois) {
            $resultats = DB::table('vente_items')
                ->join('ventes', 'vente_items.vente_id', '=', 'ventes.id')
                ->join('products', 'vente_items.product_id', '=', 'products.id')
                ->whereIn(DB::raw('MONTH(ventes.date)'), $mois)
                ->where('ventes.status', 'paye')
                ->select(
                    'products.id as product_id',
                    'products.nom as produit_nom',
                    'products.code_barre',
                    DB::raw('SUM(vente_items.quantite) as total_quantite_vendue'),
                    DB::raw('SUM(vente_items.prix_total) as total_chiffre_affaires'),
                    DB::raw('COUNT(DISTINCT ventes.id) as nombre_ventes')
                )
                ->groupBy('products.id', 'products.nom', 'products.code_barre')
                ->orderByDesc('total_quantite_vendue')
                ->limit(10)
                ->get();

            foreach ($resultats as $index => $row) {
                ProduitVenteStats::updateOrCreate(
                    ['product_id' => $row->product_id, 'saison' => $nomSaison],
                    [
                        'rang'                   => $index + 1,
                        'total_quantite_vendue'  => $row->total_quantite_vendue,
                        'total_chiffre_affaires' => $row->total_chiffre_affaires,
                        'nombre_ventes'          => $row->nombre_ventes,
                        'derniere_analyse'       => now(),
                    ]
                );
            }
        }
    }
}

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

    public function __construct() {}

    public function handle(): void
    {
        $saisons = [
            'Printemps' => [3, 4, 5],
            'Été'       => [6, 7, 8],
            'Automne'   => [9, 10, 11],
            'Hiver'     => [12, 1, 2],
        ];

        foreach ($saisons as $nomSaison => $mois) {

            // ── 1. Analyse des ventes (logique existante) ──────────────────
            $resultats = DB::table('vente_items')
                ->join('ventes', 'vente_items.vente_id', '=', 'ventes.id')
                ->join('products', 'vente_items.product_id', '=', 'products.id')
                ->whereIn(DB::raw('MONTH(ventes.date)'), $mois)
                ->where('ventes.status', 'paye')
                ->select(
                    'products.id as product_id',
                    'products.nom as produit_nom',
                    'products.code_barre',
                    'products.quantite as stock_actuel',
                    'products.seuil_alerte',
                    'products.prix_achat',
                    DB::raw('SUM(vente_items.quantite) as total_quantite_vendue'),
                    DB::raw('SUM(vente_items.prix_total) as total_chiffre_affaires'),
                    DB::raw('COUNT(DISTINCT ventes.id) as nombre_ventes'),
                    // Moyenne mensuelle sur les mois de la saison (3 mois)
                    DB::raw('ROUND(SUM(vente_items.quantite) / 3) as moyenne_mensuelle')
                )
                ->groupBy(
                    'products.id',
                    'products.nom',
                    'products.code_barre',
                    'products.quantite',
                    'products.seuil_alerte',
                    'products.prix_achat'
                )
                ->orderByDesc('total_quantite_vendue')
                ->limit(10)
                ->get();

            foreach ($resultats as $index => $row) {

                // ── 2. Mise à jour des stats (logique existante) ───────────
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

                // ── 3. Déclenchement du bon de commande ───────────────────
                $stockFaible  = $row->stock_actuel <= $row->seuil_alerte;
                $produitPopulaire = $index < 10; // tous dans ce top 10

                if ($stockFaible || $produitPopulaire) {
                    $this->creerBonDeCommande($row);
                }
            }
        }

        // ── 2. Tous les produits sous le seuil d'alerte ────────────────────
        $produitsSousSeuil = DB::table('products')
            ->whereRaw('quantite <= seuil_alerte')  // ← whereRaw au lieu de whereColumn
            ->select(
                'id as product_id',
                'nom as produit_nom',
                'code_barre',
                'quantite as stock_actuel',
                'seuil_alerte',
                'prix_achat',
                DB::raw('0 as total_quantite_vendue'),
                DB::raw('0 as total_chiffre_affaires'),
                DB::raw('0 as nombre_ventes'),
                DB::raw('seuil_alerte * 2 as moyenne_mensuelle')
            )
            ->get();

        foreach ($produitsSousSeuil as $row) {
            $this->creerBonDeCommande($row);
        }
    }

    private function creerBonDeCommande(object $row): void
    {
        // ── A. Trouver le dernier fournisseur utilisé pour ce produit ──────
        $dernierAppro = DB::table('aprovisionnement_items')
            ->join('aprovisionnements', 'aprovisionnement_items.aprovisionnement_id', '=', 'aprovisionnements.id')
            ->where('aprovisionnement_items.product_id', $row->product_id)
            ->orderByDesc('aprovisionnements.date_approvisionnement')
            ->select('aprovisionnements.fournisseur_id')
            ->first();

        // Pas de fournisseur connu → on ne peut pas créer le bon
        if (!$dernierAppro) {
            return;
        }

        $fournisseurId = $dernierAppro->fournisseur_id;

        // ── B. Anti-doublon : bon enAttente existant pour ce produit ───────
        $dejaEnAttente = DB::table('aprovisionnement_items')
            ->join('aprovisionnements', 'aprovisionnement_items.aprovisionnement_id', '=', 'aprovisionnements.id')
            ->where('aprovisionnement_items.product_id', $row->product_id)
            ->where('aprovisionnements.status', 'enAttente')
            ->exists();

        if ($dejaEnAttente) {
            return;
        }

        // ── C. Calcul de la quantité à commander ───────────────────────────
        // On commande la moyenne mensuelle de la saison (au moins 1 unité)
        $quantiteACommander = max(1, (int) $row->moyenne_mensuelle);
        $prixTotal          = $quantiteACommander * $row->prix_achat;

        // ── D. Création du bon de commande et de sa ligne ──────────────────
        DB::transaction(function () use ($row, $fournisseurId, $quantiteACommander, $prixTotal) {

            $reference = 'BC-' . strtoupper(uniqid());

            $approId = DB::table('aprovisionnements')->insertGetId([
                'reference'             => $reference,
                'fournisseur_id'        => $fournisseurId,
                'montant_total'         => $prixTotal,
                'date_approvisionnement'=> now(),
                'user_id'              => 1, // ⚠️ à remplacer par l'id d'un user système
                'status'               => 'enAttente',
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            DB::table('aprovisionnement_items')->insert([
                'aprovisionnement_id' => $approId,
                'product_id'          => $row->product_id,
                'quantite'            => $quantiteACommander,
                'prix_unitaire'       => $row->prix_achat,
                'prix_total'          => $prixTotal,
            ]);
        });
    }
}

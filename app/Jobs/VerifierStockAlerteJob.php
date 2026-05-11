<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Notifications\StockAlerteNotification;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class VerifierStockAlerteJob
{
    use Dispatchable;

    public function handle(): void
    {
        try {

            $produits = Product::whereRaw(
                'quantite <= seuil_alerte'
            )->get();

            if ($produits->isEmpty()) {

                Log::info('Aucun produit en stock critique.');

                return;
            }

            $destinataires = User::whereIn('role', [
                'admin',
                'gerant'
            ])->get();

            if ($destinataires->isEmpty()) {

                Log::warning('Aucun destinataire trouvé.');

                return;
            }

            foreach ($produits as $produit) {

                foreach ($destinataires as $user) {

                    try {

                        $dejaNotifie = $user->notifications()
                            ->whereNull('read_at')
                            ->where('data->product_id', $produit->id)
                            ->exists();

                        if (!$dejaNotifie) {
                            $user->notify(
                                new StockAlerteNotification($produit)
                            );


                            Log::info(
                                "Notification envoyée à {$user->email} pour le produit {$produit->nom}"
                            );
                        }

                    } catch (\Exception $e) {

                        Log::error(
                            "Erreur notification utilisateur {$user->id} : "
                            . $e->getMessage()
                        );
                    }
                }
            }

        } catch (\Exception $e) {

            Log::critical(
                'Erreur VerifierStockAlerteJob : '
                . $e->getMessage()
            );
        }
    }
}

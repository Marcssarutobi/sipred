<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProduitVenteStatsService;

class ProduitVenteStatsController extends Controller
{
    public function __construct(
        private readonly ProduitVenteStatsService $produitVenteStatsService
    ) {
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->produitVenteStatsService->getSeasonSummary(
                $request->input('saison'),
                (int) $request->input('limit', 10),
            )
        );
    }
}

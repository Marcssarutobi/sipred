<?php

use App\Http\Controllers\AprovisionnementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProduitVenteStatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login',[UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/produit-vente-stats', [ProduitVenteStatsController::class, 'index']);

    //Profile
    Route::get('/user/profile', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'update']);

    //CurrentUser
    Route::get('/user', [UserController::class, 'getUser']);
    //logout
    Route::post('/logout',[UserController::class, 'logout']);

    // Catégories
    Route::apiResource('categories', CategoryController::class);

    // Produits
    Route::apiResource('products', ProductController::class);

    // Mouvements
    Route::apiResource('mouvements', MouvementController::class)->except(['update']);

    // Ventes
    Route::get('ventes/statistiques', [VenteController::class, 'statistiques']);
    Route::apiResource('ventes', VenteController::class)->except(['update']);

    // Fournisseurs
    Route::apiResource('fournisseurs', FournisseurController::class);

    // Approvisionnements
    Route::apiResource('aprovisionnements', AprovisionnementController::class)->except(['update']);
    Route::post('aprovisionnement/{aprovisionnement}/livrer', [AprovisionnementController::class, 'livrer']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

});

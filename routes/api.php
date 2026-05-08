<?php

use App\Http\Controllers\AprovisionnementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login',[UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){

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
    Route::get('aprovisionnement/status', [AprovisionnementController::class, 'livrer']);

});

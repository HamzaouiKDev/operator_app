<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EchantillonStatusController; // <<< Assurez-vous que cette ligne est PRÉSENTE

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route pour l'utilisateur authentifié (standard de Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); // <<< Le "});" doit être ICI, pour fermer cette route.

// VOTRE ROUTE API CLÉ pour la mise à jour du statut.
// Elle doit être une définition de route indépendante.
Route::middleware('auth:sanctum')->put('/echantillons/{id}/complete', [EchantillonStatusController::class, 'markAsComplete']);
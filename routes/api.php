<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EchantillonController;
use App\Http\Controllers\Api\EchantillonStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/echantillons/disponibles', [EchantillonController::class, 'disponibles'])->name('echantillons.disponibles');

    // Rroute API pour changer le statut de l'échantillon à 'Complet'
    Route::post('/echantillons/{id}/marquer-complet', [EchantillonStatusController::class, 'markAsComplete'])->name('api.echantillons.markAsComplete');
    // ROUTE API pour marquer comme 'Partiel'
    Route::post('/echantillons/{echantillon}/marquer-partiel', [EchantillonStatusController::class, 'markAsPartial'])->name('api.echantillons.markAsPartial');


});
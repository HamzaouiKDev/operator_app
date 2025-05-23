<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\EnterpriseDetailsController;





Auth::routes(['register'=>false]);

Route::get('/index',  [EnterpriseDetailsController::class, 'show'])->name('home');

Route::get('/', function () {
    return view('auth.login');
});


//routes echantillons
 Route::get('/entreprise', [EnterpriseDetailsController::class, 'show'])
         ->middleware('auth')
         ->name('entreprise.show');
//Route::get('/entreprise', [EnterpriseDetailsController::class, 'show'])->name('entreprise.show');
Route::post('/entreprise/{entreprise_id}/telephone', [EnterpriseDetailsController::class, 'storeTelephone'])->name('entreprise.telephone.store');
Route::post('/entreprise/{entreprise_id}/contact', [EnterpriseDetailsController::class, 'storeContact'])->name('entreprise.contact.store');
Route::post('/entreprise/rendezvous/{echantillon_enquete_id}', [EnterpriseDetailsController::class, 'storeRendezVous'])->name('entreprise.rendezvous.store');

//Route::get('/{page}', [AdminController::class, 'index']);


?>
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppelController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TelephoneController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\EchantillonController;
use App\Http\Controllers\EnterpriseDetailsController;





Auth::routes(['register'=>false]);

Route::get('/index', [EchantillonController::class, 'index'])->middleware('auth')->name('home');

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

//routes pour l'echantillon

Route::get('/echantillons', [EchantillonController::class, 'index'])->middleware('auth')->name('echantillons.index');
Route::post('/echantillons/{id}/statut', [EchantillonController::class, 'updateStatut'])->middleware('auth')->name('echantillons.updateStatut');
Route::post('/echantillons/next', [EchantillonController::class, 'next'])->name('echantillons.next');

// routes pour les appels

Route::post('/rendezvous/store/{id}', [RendezVousController::class, 'store'])->name('rendezvous.store');
Route::post('/appels/store', [AppelController::class, 'store'])->name('appels.store');
Route::post('/appels/end', [AppelController::class, 'end'])->name('appels.end');
Route::post('/relance/store', [SuiviController::class, 'store'])->name('relance.store');
//Route::post('/echantillons/next', [EchantillonController::class, 'next'])->name('echantillons.next');
//Route::post('/echantillons/next', [EchantillonController::class, 'next'])->name('echantillons.next')->middleware('auth');


//Route::post('/rendezvous/store/{id}', [RendezVousController::class, 'store'])->name('rendezvous.store')->middleware('auth');


// Routes pour ajouter un téléphone
Route::post('/telephones/store/{entreprise_id}', [TelephoneController::class, 'store'])->name('telephones.store');

// Routes pour ajouter un contact
Route::post('/contacts/store/{entreprise_id}', [ContactController::class, 'store'])->name('contacts.store');

// Routes pour les appels
Route::post('/appels/store', [AppelController::class, 'store'])->name('appels.store');
Route::post('/appels/end', [AppelController::class, 'end'])->name('appels.end');

// Route pour la relance
Route::post('/relance/store', [SuiviController::class, 'store'])->name('relance.store');

//route pour les rendez vous 

Route::get('/rendezvous', [RendezVousController::class, 'index'])->middleware('auth')->name('rendezvous.index');
// Route pour la liste principale des rendez-vous
Route::get('/rendezvous', [RendezVousController::class, 'index'])->name('rendezvous.index');

// Route pour afficher les rendez-vous par entreprise
Route::get('/rendezvous/entreprise/{rendezVousId}', [RendezVousController::class, 'showByEntreprise'])->name('rendezvous.entreprise');


?>
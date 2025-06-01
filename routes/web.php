<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;      // Utilisé ?
use App\Http\Controllers\AppelController;       // Voir note sur la gestion des appels
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TelephoneController;
use App\Http\Controllers\EntrepriseController;  // Utilisé ?
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\EchantillonController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\EnterpriseDetailsController; // Utilisé ?

// Routes d'authentification (sans enregistrement)
Auth::routes(['register' => false]);

// Page de login par défaut
Route::get('/', function () {
    return view('auth.login');
});

// Routes nécessitant une authentification
Route::middleware(['auth'])->group(function () {

    // Page d'accueil après login (tableau de bord principal avec un échantillon)
    Route::get('/index', [EchantillonController::class, 'index'])->name('home');

    // Routes pour EchantillonController (logique principale de l'opérateur)
    Route::prefix('echantillons')->name('echantillons.')->group(function () {
        Route::get('/', [EchantillonController::class, 'index'])->name('index');
        Route::post('/next', [EchantillonController::class, 'next'])->name('next');
        Route::post('/{id}/statut', [EchantillonController::class, 'updateStatut'])->name('updateStatut');

        // Routes AJAX pour la gestion des appels (pointant vers EchantillonController)
        Route::get('/appel/encours', [EchantillonController::class, 'appelEnCours'])->name('appelEnCours');
        Route::post('/appel/demarrer', [EchantillonController::class, 'demarrerAppel'])->name('demarrerAppel');
        Route::post('/appel/terminer', [EchantillonController::class, 'terminerAppel'])->name('terminerAppel');
    });

    // Routes pour la gestion des RendezVous
    Route::prefix('rendezvous')->name('rendezvous.')->group(function () {
        Route::get('/', [RendezVousController::class, 'index'])->name('index');
        Route::get('/entreprise/{rendezVousId}', [RendezVousController::class, 'showByEntreprise'])->name('entreprise');
        Route::post('/store/{id}', [RendezVousController::class, 'store'])->name('store');
        Route::get('/aujourdhui', [RendezVousController::class, 'aujourdhui'])->name('aujourdhui');
    });

    // Routes pour ajouter des informations à une entreprise
    Route::post('/telephones/store/{entreprise_id}', [TelephoneController::class, 'store'])->name('telephones.store');
    Route::post('/contacts/store/{entreprise_id}', [ContactController::class, 'store'])->name('contacts.store');
    
    Route::prefix('emails')->name('emails.')->group(function () {
        Route::post('/store/{entreprise_id}', [EmailController::class, 'store'])->name('store');
        Route::put('/{id}', [EmailController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmailController::class, 'destroy'])->name('destroy');
    });

    // Routes pour SuiviController
    Route::post('/relance/store', [SuiviController::class, 'store'])->name('relance.store'); // Votre route existante pour les suivis
    Route::post('/suivis/creer-rappel', [SuiviController::class, 'creerRappel'])->name('suivis.creerRappel'); // ✅ ROUTE AJOUTÉE ICI

    // Route des statistiques
    Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');


//    Route::get('/entreprises/{entreprise}', [RendezVousController::class, 'showEntreprisePage'])->name('entreprise.show');

    // --- Routes commentées à vérifier ---
    // Route::get('/entreprise', [EnterpriseDetailsController::class, 'show'])->name('entreprise.show');
    // Route::post('/entreprise/{entreprise_id}/telephone', [EnterpriseDetailsController::class, 'storeTelephone'])->name('entreprise.telephone.store');
    // Route::post('/entreprise/{entreprise_id}/contact', [EnterpriseDetailsController::class, 'storeContact'])->name('entreprise.contact.store');
    // Route::post('/entreprise/rendezvous/{echantillon_enquete_id}', [EnterpriseDetailsController::class, 'storeRendezVous'])->name('entreprise.rendezvous.store');
    // Route::get('/{page}', [AdminController::class, 'index']);
});

// Routes API
Route::prefix('api')->name('api.')->group(function () {
    // Si cette route nécessite une authentification et utilise la session web,
    // elle pourrait être mieux placée dans le groupe middleware('auth') ci-dessus.
    // Si elle utilise une authentification par token (Sanctum), alors elle est bien ici avec ->middleware('auth:sanctum').
    Route::get('/echantillons/disponibles', [EchantillonController::class, 'disponibles'])->name('echantillons.disponibles');
});
// Dans le groupe middleware('auth')
Route::post('/telephones/{telephone}/update-status', [App\Http\Controllers\TelephoneController::class, 'updateStatus'])->name('telephones.updateStatus');
Route::post('/telephones/get-or-create-for-contact', [TelephoneController::class, 'getOrCreateForContact'])->name('telephones.getOrCreateForContact');

// Dans routes/web.php, à l'intérieur de Route::middleware(['auth'])->group(...)

Route::prefix('echantillons')->name('echantillons.')->group(function () {
    // ... autres routes echantillons ...
    Route::post('/appel/demarrer', [EchantillonController::class, 'demarrerAppel'])->name('demarrerAppel');
    Route::post('/{echantillon}/refus', [EchantillonController::class, 'markAsRefused'])->name('echantillons.refus');
    // ...
});

Route::post('/relances', [SuiviController::class, 'creerRappel'])->name('relances.store');

Route::post('/suivis', [SuiviController::class, 'store'])->name('suivis.store');

?>
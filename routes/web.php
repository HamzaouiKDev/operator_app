<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Import des contrôleurs
use App\Http\Controllers\MailController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TelephoneController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\EchantillonController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\Admin\EnqueteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EntrepriseImportController;
use App\Http\Controllers\Admin\EchantillonImportController;


/*
|--------------------------------------------------------------------------
| Routes Publiques et Authentification
|--------------------------------------------------------------------------
|
| Ces routes sont accessibles par tout le monde.
|
*/

// La racine du site redirige vers la page de connexion
Route::get('/', function () {
    return view('auth.login');
});

// Génère les routes d'authentification (login, logout, etc.) mais désactive l'enregistrement.
Auth::routes(['register' => false]);


/*
|--------------------------------------------------------------------------
| Routes Protégées par Authentification
|--------------------------------------------------------------------------
|
| L'utilisateur doit être connecté pour accéder à toutes les routes
| définies à l'intérieur de ce groupe.
|
*/

Route::middleware(['auth'])->group(function () {

    //======================================================================
    // == GROUPE ADMINISTRATEUR ==
    // Seuls les utilisateurs avec le rôle 'Admin' peuvent accéder à ces routes.
    //======================================================================
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Note : Le dashboard de l'admin ne devrait probablement pas être celui des échantillons.
        // Vous devriez créer un contrôleur et une vue spécifiques pour le dashboard admin.
        // Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des utilisateurs (CRUD complet)
        Route::resource('users', UserController::class);
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/entreprises/import', [EntrepriseImportController::class, 'create'])->name('entreprises.import.form');
        Route::post('/entreprises/import/store-telephones', [EntrepriseImportController::class, 'storeTelephones'])->name('entreprises.import.telephones');
        Route::post('/admin/entreprises/import/emails', [EntrepriseImportController::class, 'storeEmails'])->name('entreprises.import.emails');
        Route::post('/entreprises/import', [EntrepriseImportController::class, 'store'])->name('entreprises.import.store');
         // AJOUT : ROUTES POUR L'IMPORT DES ÉCHANTILLONS
        Route::get('/echantillons/import', [EchantillonImportController::class, 'create'])->name('echantillons.import.form');
        Route::post('/echantillons/import', [EchantillonImportController::class, 'store'])->name('echantillons.import.store');
        Route::resource('enquetes', EnqueteController::class);
        Route::post('/contacts/import', [EntrepriseImportController::class, 'storeContacts'])->name('contacts.import.store');
    });
    //======================================================================
    // == GROUPE SUPERVISEUR ==  ✅ NOUVEAU BLOC À AJOUTER
    // Seuls les utilisateurs avec le rôle 'Superviseur' (et 'Admin' pour la visibilité) peuvent y accéder.
    //======================================================================
    Route::middleware(['role:Superviseur|Admin'])->prefix('supervisor')->name('supervisor.')->group(function () {

        // Affiche le tableau de bord avec le sélecteur de téléopérateur
        // et les statistiques si un opérateur est choisi.
        Route::get('/dashboard', [App\Http\Controllers\SupervisorController::class, 'index'])
             ->name('dashboard');

    });

    //======================================================================
    // == GROUPE TÉLÉOPÉRATEUR ==
    // Seuls les utilisateurs avec le rôle 'Téléopérateur' peuvent accéder à ces routes.
    //======================================================================
    Route::middleware(['role:Téléopérateur'])->group(function () {

        // Page d'accueil après connexion pour le téléopérateur
        Route::get('/index', [EchantillonController::class, 'index'])->name('home');

        // --- Gestion des Échantillons et des Appels ---
        Route::prefix('echantillons')->name('echantillons.')->group(function () {
            Route::get('/en-attente', [EchantillonController::class, 'listeEnAttente'])->name('en_attente');
            Route::get('/', [EchantillonController::class, 'index'])->name('index');
            Route::get('/{echantillon}', [EchantillonController::class, 'show'])->name('show');
            Route::post('/next', [EchantillonController::class, 'next'])->name('next');
            Route::post('/{id}/statut', [EchantillonController::class, 'updateStatut'])->name('updateStatut');
            Route::post('/{echantillon}/refus', [EchantillonController::class, 'markAsRefused'])->name('refus');
            Route::post('/{id}/marquer-impossible', [EchantillonController::class, 'markAsImpossible'])->name('markImpossible');

            // Logique d'appel
            Route::get('/appel/encours', [EchantillonController::class, 'appelEnCours'])->name('appelEnCours');
            Route::post('/appel/demarrer', [EchantillonController::class, 'demarrerAppel'])->name('demarrerAppel');
            Route::post('/appel/terminer', [EchantillonController::class, 'terminerAppel'])->name('terminerAppel');
          
        });


        // --- Gestion des Rendez-vous ---
        Route::prefix('rendezvous')->name('rendezvous.')->group(function () {
            Route::get('/', [RendezVousController::class, 'index'])->name('index');
            Route::get('/entreprise/{rendezVousId}', [RendezVousController::class, 'showByEntreprise'])->name('entreprise');
            Route::post('/store/{id}', [RendezVousController::class, 'store'])->name('store');
            Route::get('/aujourdhui', [RendezVousController::class, 'aujourdhui'])->name('aujourdhui');
        });

        // --- Gestion des Suivis et Relances ---
        Route::prefix('suivis')->name('suivis.')->group(function () {
            Route::get('/', [SuiviController::class, 'indexSuivis'])->name('index');
            Route::post('/', [SuiviController::class, 'store'])->name('store');
            Route::post('/creer-rappel', [SuiviController::class, 'creerRappel'])->name('creerRappel');
        });

        // --- Ajout d'informations (Contacts, Téléphones, Emails) ---
        Route::post('/telephones/store/{entreprise_id}', [TelephoneController::class, 'store'])->name('telephones.store');
        Route::post('/telephones/{telephone}/update-status', [TelephoneController::class, 'updateStatus'])->name('telephones.updateStatus');
        Route::post('/telephones/get-or-create-for-contact', [TelephoneController::class, 'getOrCreateForContact'])->name('telephones.getOrCreateForContact');
        
        Route::post('/contacts/store/{entreprise_id}', [ContactController::class, 'store'])->name('contacts.store');
        
        Route::prefix('emails')->name('emails.')->group(function () {
            Route::post('/store/{entreprise_id}', [EmailController::class, 'store'])->name('store');
            Route::put('/{id}', [EmailController::class, 'update'])->name('update');
            Route::delete('/{id}', [EmailController::class, 'destroy'])->name('destroy');
        });

        // --- Statistiques ---
        Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
        // --- Route du flux SSE pour les notifications (MAINTENANT DANS CE GROUPE) ---
        // Ceci garantit que seuls les utilisateurs avec le rôle 'Téléopérateur' peuvent y accéder.
       // Route::get('/notifications-stream', [NotificationController::class, 'streamUpcomingRendezVous'])
         //   ->name('notifications.stream');


    });
});

/////////////////////////// Route pour l'envoi de mail

// Cette route gère l'action d'envoyer l'e-mail
// ROUTE CORRECTE
Route::post('/emails/send', [MailController::class, 'sendBilingualEmail'])->name('emails.send');


/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
|
| Ces routes sont généralement sans état et utilisent une authentification
| par token (comme Sanctum).
|
*/

Route::prefix('api')->name('api.')->group(function () {
    // Note : Pour sécuriser cette route, vous devriez utiliser le middleware 'auth:sanctum'.
    // Si elle doit utiliser l'authentification web normale, elle devrait être dans le groupe du dessus.
    Route::get('/echantillons/disponibles', [EchantillonController::class, 'disponibles'])->name('echantillons.disponibles');
});

// routes/web.php

Route::get('/verification-php-info', function () {
    phpinfo();
});

/*
|--------------------------------------------------------------------------
| Routes à vérifier
|--------------------------------------------------------------------------
|
| Ces routes étaient commentées dans votre fichier original.
| Vous devriez vérifier si elles sont encore utiles ou si elles peuvent
| être supprimées.
|
*/

// Route::get('/entreprise', [EnterpriseDetailsController::class, 'show'])->name('entreprise.show');
// Route::post('/entreprise/{entreprise_id}/telephone', [EnterpriseDetailsController::class, 'storeTelephone'])->name('entreprise.telephone.store');
// Route::post('/entreprise/{entreprise_id}/contact', [EnterpriseDetailsController::class, 'storeContact'])->name('entreprise.contact.store');
// Route::post('/entreprise/rendezvous/{echantillon_enquete_id}', [EnterpriseDetailsController::class, 'storeRendezVous'])->name('entreprise.rendezvous.store');
// Route::get('/{page}', [AdminController::class, 'index']);
//Route::get('/entreprises/{entreprise}', [RendezVousController::class, 'showEntreprisePage'])->name('entreprise.show');
// Route::post('/relance/store', [SuiviController::class, 'store'])->name('relance.store'); // Probablement un doublon de suivis.store



<?php

use Illuminate\Support\Facades\Route;

// --- Imports de tous les Contrôleurs ---
use App\Http\Controllers\ProfileController;

// MODULE 1 : HUMAIN
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StagiaireController;

// MODULE 2 : RH & SÉCURITÉ
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\HistoriquePassageController;

// MODULE 3 : PRODUCTION
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\FeuilleTempsController;
use App\Http\Controllers\LivrableController;
use App\Http\Controllers\TechnologieController;

// MODULE 4 : FORMATION
use App\Http\Controllers\CatalogueFormationController;
use App\Http\Controllers\SessionFormationController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\EvaluationSessionController;
use App\Http\Controllers\SupportCoursController;

// MODULE 5 : ACTIFS IT
use App\Http\Controllers\TypeMaterielController;
use App\Http\Controllers\AssetMaterielController;
use App\Http\Controllers\TicketMaintenanceController;
use App\Http\Controllers\LicenceLogicielController;
use App\Http\Controllers\AssignationMaterielController;
use App\Http\Controllers\AssignationLicenceController;

// MODULE 6 : FINANCE
use App\Http\Controllers\CategorieFluxController;
use App\Http\Controllers\FluxTresorerieController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\LigneFactureController;
use App\Http\Controllers\FichePaieController;
use App\Http\Controllers\NoteDeFraisController;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes & Outils de Dev
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// ROUTE DE CONNEXION TEMPORAIRE POUR TES TESTS POSTMAN
Route::get('/dev-login', function () {
    $admin = User::where('email', 'admin@entreprise.com')->first();
    if ($admin) {
        Auth::login($admin);
        return response()->json([
            'status' => 'success',
            'message' => 'Authentifié en Admin avec succès !',
            'user' => $admin->nom_complet
        ]);
    }
    return response()->json(['status' => 'error', 'message' => 'Admin introuvable'], 404);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ROUTES SÉCURISÉES PAR GRANULARITÉ FINE (Standards du Monde Réel)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ==========================================
    // --- MODULE 1 : HUMAIN ---
    // ==========================================
    Route::middleware(['permission:view-humain'])->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'show']);
        Route::resource('employes', EmployeController::class)->only(['index', 'show']);
        Route::resource('stagiaires', StagiaireController::class)->only(['index', 'show']);
        Route::resource('clients', ClientController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-humain'])->group(function () {
        Route::resource('users', UserController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('employes', EmployeController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('stagiaires', StagiaireController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('clients', ClientController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-humain'])->group(function () {
        Route::resource('users', UserController::class)->only(['destroy']);
        Route::resource('employes', EmployeController::class)->only(['destroy']);
        Route::resource('stagiaires', StagiaireController::class)->only(['destroy']);
        Route::resource('clients', ClientController::class)->only(['destroy']);
    });

    // ==========================================
    // --- MODULE 2 : RH & SÉCURITÉ PHYSIQUE ---
    // ==========================================
    Route::middleware(['permission:view-rh'])->group(function () {
        Route::resource('departements', DepartementController::class)->only(['index', 'show']);
        Route::resource('contrats', ContratController::class)->only(['index', 'show']);
        Route::resource('zones', ZoneController::class)->only(['index', 'show']);
        Route::resource('historique-passages', HistoriquePassageController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-rh'])->group(function () {
        Route::resource('departements', DepartementController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('contrats', ContratController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('zones', ZoneController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('historique-passages', HistoriquePassageController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-rh'])->group(function () {
        Route::resource('departements', DepartementController::class)->only(['destroy']);
        Route::resource('contrats', ContratController::class)->only(['destroy']);
        Route::resource('zones', ZoneController::class)->only(['destroy']);
        Route::resource('historique-passages', HistoriquePassageController::class)->only(['destroy']);
    });

    // Pointages : Isolation complète pour permettre aux salariés de badger (écriture)
    Route::middleware(['permission:view-pointages'])->group(function () {
        Route::resource('pointages', PointageController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-pointages'])->group(function () {
        Route::resource('pointages', PointageController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-pointages'])->group(function () {
        Route::resource('pointages', PointageController::class)->only(['destroy']);
    });

    // ==========================================
    // --- MODULE 3 : PRODUCTION ---
    // ==========================================
    Route::middleware(['permission:view-production'])->group(function () {
        Route::resource('projets', ProjetController::class)->only(['index', 'show']);
        Route::resource('taches', TacheController::class)->only(['index', 'show']);
        Route::resource('feuille_temps', FeuilleTempsController::class)->only(['index', 'show']);
        Route::resource('livrables', LivrableController::class)->only(['index', 'show']);
        Route::resource('technologies', TechnologieController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-production'])->group(function () {
        Route::resource('projets', ProjetController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('taches', TacheController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('feuille_temps', FeuilleTempsController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('livrables', LivrableController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('technologies', TechnologieController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-production'])->group(function () {
        Route::resource('projets', ProjetController::class)->only(['destroy']);
        Route::resource('taches', TacheController::class)->only(['destroy']);
        Route::resource('feuille_temps', FeuilleTempsController::class)->only(['destroy']);
        Route::resource('livrables', LivrableController::class)->only(['destroy']);
        Route::resource('technologies', TechnologieController::class)->only(['destroy']);
    });

    // ==========================================
    // --- MODULE 4 : FORMATION ---
    // ==========================================
    Route::middleware(['permission:view-formation'])->group(function () {
        Route::resource('catalogue', CatalogueFormationController::class)->only(['index', 'show']);
        Route::resource('sessions', SessionFormationController::class)->only(['index', 'show']);
        Route::resource('inscriptions', InscriptionController::class)->only(['index', 'show']);
        Route::resource('supports', SupportCoursController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-formation'])->group(function () {
        Route::resource('catalogue', CatalogueFormationController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('sessions', SessionFormationController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('inscriptions', InscriptionController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('supports', SupportCoursController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-formation'])->group(function () {
        Route::resource('catalogue', CatalogueFormationController::class)->only(['destroy']);
        Route::resource('sessions', SessionFormationController::class)->only(['destroy']);
        Route::resource('inscriptions', InscriptionController::class)->only(['destroy']);
        Route::resource('supports', SupportCoursController::class)->only(['destroy']);
    });

    // Évaluations
    Route::middleware(['permission:view-evaluations'])->group(function () {
        Route::resource('evaluations', EvaluationSessionController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-evaluations'])->group(function () {
        Route::resource('evaluations', EvaluationSessionController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-evaluations'])->group(function () {
        Route::resource('evaluations', EvaluationSessionController::class)->only(['destroy']);
    });

    // ==========================================
    // --- MODULE 5 : ACTIFS IT ---
    // ==========================================
    Route::middleware(['permission:view-assets'])->group(function () {
        Route::resource('type_materiels', TypeMaterielController::class)->only(['index', 'show']);
        Route::resource('assets', AssetMaterielController::class)->only(['index', 'show']);
        Route::resource('tickets', TicketMaintenanceController::class)->only(['index', 'show']);
        Route::resource('licences', LicenceLogicielController::class)->only(['index', 'show']);
        Route::resource('assignation_materiels', AssignationMaterielController::class)->only(['index', 'show']);
        Route::resource('assignation_licences', AssignationLicenceController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-assets'])->group(function () {
        Route::resource('type_materiels', TypeMaterielController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('assets', AssetMaterielController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('tickets', TicketMaintenanceController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('licences', LicenceLogicielController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('assignation_materiels', AssignationMaterielController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('assignation_licences', AssignationLicenceController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-assets'])->group(function () {
        Route::resource('type_materiels', TypeMaterielController::class)->only(['destroy']);
        Route::resource('assets', AssetMaterielController::class)->only(['destroy']);
        Route::resource('tickets', TicketMaintenanceController::class)->only(['destroy']);
        Route::resource('licences', LicenceLogicielController::class)->only(['destroy']);
        Route::resource('assignation_materiels', AssignationMaterielController::class)->only(['destroy']);
        Route::resource('assignation_licences', AssignationLicenceController::class)->only(['destroy']);
    });

    // ==========================================
    // --- MODULE 6 : FINANCE ---
    // ==========================================
    Route::middleware(['permission:view-finance'])->group(function () {
        Route::resource('categorie_flux', CategorieFluxController::class)->only(['index', 'show']);
        Route::resource('flux_tresoreries', FluxTresorerieController::class)->only(['index', 'show']);
        Route::resource('factures', FactureController::class)->only(['index', 'show']);
        Route::resource('ligne_factures', LigneFactureController::class)->only(['index', 'show']);
        Route::resource('fiche_paies', FichePaieController::class)->only(['index', 'show']);
        Route::resource('note_de_frais', NoteDeFraisController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit-finance'])->group(function () {
        Route::resource('categorie_flux', CategorieFluxController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('flux_tresoreries', FluxTresorerieController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('factures', FactureController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('ligne_factures', LigneFactureController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('fiche_paies', FichePaieController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('note_de_frais', NoteDeFraisController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware(['permission:delete-finance'])->group(function () {
        Route::resource('categorie_flux', CategorieFluxController::class)->only(['destroy']);
        Route::resource('flux_tresoreries', FluxTresorerieController::class)->only(['destroy']);
        Route::resource('factures', FactureController::class)->only(['destroy']);
        Route::resource('ligne_factures', LigneFactureController::class)->only(['destroy']);
        Route::resource('fiche_paies', FichePaieController::class)->only(['destroy']);
        Route::resource('note_de_frais', NoteDeFraisController::class)->only(['destroy']);
    });

});

require __DIR__.'/auth.php';
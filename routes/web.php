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

Route::get('/', function () {
    return view('welcome');
});

// ROUTAGE STANDARD (Laravel Breeze)
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route de badge rapide (Widget Pointage)
Route::post('/pointages/badge', [App\Http\Controllers\PointageController::class, 'badge'])
    ->middleware('auth')
    ->name('pointages.badge');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ROUTES SÉCURISÉES - ARCHITECTURE GLOBALE
    | (Permissions gérées dans le __construct de chaque contrôleur)
    |--------------------------------------------------------------------------
    */
    
    // --- MODULE 1 : HUMAIN ---
    Route::resource('users', UserController::class);
    Route::resource('employes', EmployeController::class);
    Route::resource('stagiaires', StagiaireController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('departements', DepartementController::class);

    // --- MODULE 5 : ACTIFS IT ---
    // assets.* = AssetMaterielController (URL: /assets, noms: assets.index, assets.show, etc.)
    Route::resource('assets', AssetMaterielController::class);
    Route::resource('licences', LicenceLogicielController::class);
    Route::resource('tickets', TicketMaintenanceController::class);
    
    // Helpdesk - Statut du ticket
    Route::patch('tickets/{ticket}/statut', [TicketMaintenanceController::class, 'updateStatut'])->name('tickets.statut');
    
    // Assignations et restitutions (Matériels)
    Route::post('assets/{asset}/assigner', [AssignationMaterielController::class, 'store'])->name('assignation_materiels.store');
    Route::patch('assignations/{id}/restituer', [AssignationMaterielController::class, 'restituer'])->name('assignation_materiels.restituer');
    
    // Assignations et révocations (Licences)
    Route::post('licences/{licence}/assigner', [AssignationLicenceController::class, 'store'])->name('assignation_licences.store');
    Route::patch('assignations-licences/{id}/revoquer', [AssignationLicenceController::class, 'revoquer'])->name('assignation_licences.revoquer');


    // --- MODULE 2 : RH & SÉCURITÉ PHYSIQUE ---
    Route::resource('departements', DepartementController::class);
    Route::resource('contrats', ContratController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('historique-passages', HistoriquePassageController::class);
    Route::resource('pointages', PointageController::class);

    // --- MODULE 3 : PRODUCTION ---
    Route::resource('projets', ProjetController::class);
    Route::resource('taches', TacheController::class);
    Route::resource('feuille_temps', FeuilleTempsController::class);
    Route::resource('livrables', LivrableController::class);
    Route::resource('technologies', TechnologieController::class);
    // Route Kanban : mise à jour du statut d'une tâche sur le pivot (asynchrone)
    Route::patch('projets/{projet}/taches/{tache}/statut', [ProjetController::class, 'updateTacheStatut'])->name('projets.taches.statut');
    // Route de téléchargement sécurisé d'un livrable
    Route::get('livrables/{livrable}/download', [LivrableController::class, 'download'])->name('livrables.download');

    // --- MODULE 4 : FORMATION ---
    Route::resource('catalogue', CatalogueFormationController::class);
    Route::resource('sessions', SessionFormationController::class);
    Route::resource('inscriptions', InscriptionController::class);
    // Route asynchrone (Alpine Fetch) pour modifier le statut d'inscription
    Route::patch('inscriptions/{inscription}/statut', [InscriptionController::class, 'updateStatut'])->name('inscriptions.statut');
    
    Route::resource('supports', SupportCoursController::class);
    // Route de téléchargement sécurisé du support
    Route::get('supports/{support}/download', [SupportCoursController::class, 'download'])->name('supports.download');
    
    Route::resource('evaluations', EvaluationSessionController::class);

    // --- MODULE 5 : ACTIFS IT (tables de config) ---
    Route::resource('type_materiels', TypeMaterielController::class);
    Route::resource('assignation_materiels', AssignationMaterielController::class);
    Route::resource('assignation_licences', AssignationLicenceController::class);

    // --- PARAMETRES ADMIN ---
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/parametres', function () {
            $technologies = \App\Models\Technologie::orderBy('nom_tech')->get();
            $typeMaterialels = \App\Models\TypeMateriel::orderBy('libelle_type')->get();
            $categoriesFlux = \App\Models\CategorieFlux::orderBy('libelle_categorie')->get();
            return view('admin.parametres', compact('technologies', 'typeMaterialels', 'categoriesFlux'));
        })->name('admin.parametres');
    });

    // --- MODULE 6 : FINANCE ---
    Route::resource('categorie_flux', CategorieFluxController::class);
    Route::resource('flux_tresoreries', FluxTresorerieController::class);
    Route::resource('factures', FactureController::class);
    Route::patch('factures/{facture}/statut', [FactureController::class, 'updateStatut'])->name('factures.statut');
    
    Route::resource('ligne_factures', LigneFactureController::class);
    
    Route::resource('fiche_paies', FichePaieController::class);
    Route::patch('fiche_paies/{fiche}/payer', [FichePaieController::class, 'payer'])->name('fiche_paies.payer');
    
    Route::resource('note_de_frais', NoteDeFraisController::class);
    Route::patch('note_de_frais/{note}/statut', [NoteDeFraisController::class, 'updateStatut'])->name('note_de_frais.statut');
    Route::get('note_de_frais/{note}/download', [NoteDeFraisController::class, 'download'])->name('note_de_frais.download');
});

require __DIR__.'/auth.php';
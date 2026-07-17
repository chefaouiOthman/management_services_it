<?php

use Illuminate\Support\Facades\Route;

// --- Imports de tous les Contrôleurs ---
use App\Http\Controllers\ProfileController;

// GESTION DYNAMIQUE DES RÔLES
use App\Http\Controllers\RoleController;

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
use App\Http\Controllers\ProjetTacheController;

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
    return redirect()->route('login');
});

// ============================================================
// ROUTES ACCESSIBLES À TOUS LES UTILISATEURS AUTHENTIFIÉS
// (Chaque contrôleur filtre les données par rôle en interne)
// ============================================================
Route::middleware('auth')->group(function () {

    // --- Dashboard & Profil ---
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('verified')->name('dashboard');

    Route::post('/pointages/badge', [App\Http\Controllers\PointageController::class, 'badge'])
        ->name('pointages.badge');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MODULE 1 : HUMAIN (self-profile) ---
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

    // --- MODULE 2 : RH & SÉCURITÉ (consultation) ---
    Route::get('pointages', [PointageController::class, 'index'])->name('pointages.index');
    Route::get('departements', [DepartementController::class, 'index'])->name('departements.index');
    Route::get('departements/{departement}', [DepartementController::class, 'show'])->name('departements.show');
    Route::get('zones', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('zones/{zone}', [ZoneController::class, 'show'])->name('zones.show');

    // --- MODULE 3 : PRODUCTION (consultation filtrée) ---
    Route::get('projets', [ProjetController::class, 'index'])->name('projets.index');
    Route::get('projets/{projet}', [ProjetController::class, 'show'])->name('projets.show');
    Route::get('taches', [TacheController::class, 'index'])->name('taches.index');
    Route::get('taches/{tache}', [TacheController::class, 'show'])->name('taches.show');
    Route::get('feuille_temps', [FeuilleTempsController::class, 'index'])->name('feuille_temps.index');
    Route::get('feuille_temps/{feuille_temp}', [FeuilleTempsController::class, 'show'])->name('feuille_temps.show');
    Route::get('feuille_temps/select_project', [FeuilleTempsController::class, 'selectProject'])->name('feuille_temps.select_project');
    Route::get('technologies', [TechnologieController::class, 'index'])->name('technologies.index');
    Route::get('technologies/{technologie}', [TechnologieController::class, 'show'])->name('technologies.show');

    // --- MODULE 4 : FORMATION (tous les rôles y accèdent) ---
    Route::resource('catalogue', CatalogueFormationController::class)->only(['index', 'show']);
    Route::resource('sessions', SessionFormationController::class)->only(['index', 'show']);
    Route::resource('supports', SupportCoursController::class)->only(['index', 'show']);
    Route::get('supports/{support}/download', [SupportCoursController::class, 'download'])->name('supports.download');

    // --- MODULE 5 : ACTIFS IT (consultation) ---
    Route::get('assets', [AssetMaterielController::class, 'index'])->name('assets.index');
    Route::get('assets/{asset}', [AssetMaterielController::class, 'show'])->name('assets.show');
    Route::get('type_materiels', [TypeMaterielController::class, 'index'])->name('type_materiels.index');
    Route::get('type_materiels/{type_materiel}', [TypeMaterielController::class, 'show'])->name('type_materiels.show');
    Route::get('licences', [LicenceLogicielController::class, 'index'])->name('licences.index');
    Route::get('licences/{licence}', [LicenceLogicielController::class, 'show'])->name('licences.show');

    // Tickets : consultation + création (ouverte à Employe_Standard et Stagiaire)
    Route::get('tickets', [TicketMaintenanceController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [TicketMaintenanceController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [TicketMaintenanceController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [TicketMaintenanceController::class, 'show'])->name('tickets.show');

    // --- MODULE 6 : FINANCE (self-service) ---
    Route::get('factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    Route::get('fiche_paies', [FichePaieController::class, 'index'])->name('fiche_paies.index');
    Route::get('fiche_paies/{fiche_paie}', [FichePaieController::class, 'show'])->name('fiche_paies.show');
    Route::get('note_de_frais', [NoteDeFraisController::class, 'index'])->name('note_de_frais.index');
    Route::get('note_de_frais/{note_de_frai}', [NoteDeFraisController::class, 'show'])->name('note_de_frais.show');
    Route::get('note_de_frais/{note_de_frai}/download', [NoteDeFraisController::class, 'download'])->name('note_de_frais.download');
});

// ============================================================
// ROUTES ADMINISTRATION (Super Admin + Admin UNIQUEMENT)
// Toute route non listée ci-dessous est bloquée par défaut
// pour les rôles Employe_Standard, Stagiaire et Client.
// ============================================================
Route::middleware(['auth', 'role:Super Admin|Admin'])->group(function () {

    // --- GESTION DYNAMIQUE DES RÔLES (Super Admin only in controller) ---
    Route::resource('roles', RoleController::class);

    // --- MODULE 1 : HUMAIN ---
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('employes', EmployeController::class);
    Route::resource('stagiaires', StagiaireController::class);
    Route::resource('clients', ClientController::class);

    // --- MODULE 2 : RH & SÉCURITÉ PHYSIQUE ---
    Route::resource('departements', DepartementController::class)->except(['index', 'show']);
    Route::resource('contrats', ContratController::class);
    Route::resource('zones', ZoneController::class)->except(['index', 'show']);
    Route::resource('historique-passages', HistoriquePassageController::class)->names('historique_passages');
    Route::resource('pointages', PointageController::class)->except(['index']);

    // --- MODULE 3 : PRODUCTION (mutations) ---
    Route::resource('projets', ProjetController::class)->except(['index', 'show']);
    Route::resource('projets.taches', ProjetTacheController::class)->except(['index', 'show']);
    Route::patch('projets/{projet}/taches/{tache}/statut', [ProjetController::class, 'updateTacheStatut'])->name('projets.taches.statut');

    Route::resource('taches', TacheController::class)->except(['index', 'show']);
    Route::get('projets/{projet}/feuille_temps/create', [FeuilleTempsController::class, 'create'])->name('projets.feuille_temps.create');
    Route::post('projets/{projet}/feuille_temps', [FeuilleTempsController::class, 'store'])->name('projets.feuille_temps.store');
    Route::resource('feuille_temps', FeuilleTempsController::class)->except(['create', 'store', 'selectProject', 'index', 'show']);

    Route::get('projets/{projet}/livrables/create', [LivrableController::class, 'createForProject'])->name('projets.livrables.create');
    Route::post('projets/{projet}/livrables', [LivrableController::class, 'storeForProject'])->name('projets.livrables.store');
    Route::resource('livrables', LivrableController::class);
    Route::get('livrables/{livrable}/download', [LivrableController::class, 'download'])->name('livrables.download');

    Route::resource('technologies', TechnologieController::class)->except(['index', 'show']);

    // --- MODULE 4 : FORMATION (mutations) ---
    Route::resource('catalogue', CatalogueFormationController::class)->except(['index', 'show']);
    Route::resource('sessions', SessionFormationController::class)->except(['index', 'show']);
    Route::resource('supports', SupportCoursController::class)->except(['index', 'show']);
    Route::resource('inscriptions', InscriptionController::class);
    Route::patch('inscriptions/{inscription}/statut', [InscriptionController::class, 'updateStatut'])->name('inscriptions.statut');
    Route::resource('evaluations', EvaluationSessionController::class);

    // --- MODULE 5 : ACTIFS IT (mutations) ---
    Route::resource('assets', AssetMaterielController::class)->except(['index', 'show']);
    Route::resource('type_materiels', TypeMaterielController::class)->except(['index', 'show']);
    Route::resource('licences', LicenceLogicielController::class)->except(['index', 'show']);
    Route::get('tickets/{ticket}/edit', [TicketMaintenanceController::class, 'edit'])->name('tickets.edit');
    Route::put('tickets/{ticket}', [TicketMaintenanceController::class, 'update'])->name('tickets.update');
    Route::delete('tickets/{ticket}', [TicketMaintenanceController::class, 'destroy'])->name('tickets.destroy');
    Route::patch('tickets/{ticket}/statut', [TicketMaintenanceController::class, 'updateStatut'])->name('tickets.statut');

    // Assignations et restitutions (Matériels)
    Route::post('assets/{asset}/assigner', [AssignationMaterielController::class, 'store'])->name('assignation_materiels.store');
    Route::patch('assignations/{id}/restituer', [AssignationMaterielController::class, 'restituer'])->name('assignation_materiels.restituer');
    Route::resource('assignation_materiels', AssignationMaterielController::class);

    // Assignations et révocations (Licences)
    Route::post('licences/{licence_id}/assigner', [AssignationLicenceController::class, 'store'])->name('assignation_licences.store');
    Route::patch('assignations-licences/{id}/revoquer', [AssignationLicenceController::class, 'revoquer'])->name('assignation_licences.revoquer');
    Route::resource('assignation_licences', AssignationLicenceController::class);

    // --- MODULE 6 : FINANCE (mutations) ---
    Route::resource('categorie_flux', CategorieFluxController::class);
    Route::resource('flux_tresoreries', FluxTresorerieController::class);
    Route::resource('factures', FactureController::class)->except(['index', 'show']);
    Route::patch('factures/{facture}/statut', [FactureController::class, 'updateStatut'])->name('factures.statut');
    Route::resource('ligne_factures', LigneFactureController::class);
    Route::resource('fiche_paies', FichePaieController::class)->except(['index', 'show']);
    Route::patch('fiche_paies/{fiche}/payer', [FichePaieController::class, 'payer'])->name('fiche_paies.payer');
    Route::resource('note_de_frais', NoteDeFraisController::class)->except(['index', 'show']);
    Route::patch('note_de_frais/{note}/statut', [NoteDeFraisController::class, 'updateStatut'])->name('note_de_frais.statut');
});

require __DIR__.'/auth.php';

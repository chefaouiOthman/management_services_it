<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\FeuilleTempsController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\TicketMaintenanceController;
use App\Http\Controllers\CatalogueFormationController;
use App\Http\Controllers\SessionFormationController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\NoteFraisController;
use App\Http\Controllers\TresorerieController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
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
| Gestion des Droits, Cloisonnement & Sécurité (Système Global Connecté)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- ZONE ADMIN (Contrôle d'Entreprise Total) ---
    Route::middleware(['role:Admin'])->group(function () {
        // Module 1 : RH & Tiers
        Route::resource('employes', EmployeController::class);
        Route::resource('stagiaires', StagiaireController::class);
        Route::resource('clients', ClientController::class);

        // Module 2 : Gestion de Production
        Route::resource('projets', ProjetController::class);
        Route::resource('taches', TacheController::class);

        // Module Assets : Parc Informatique
        Route::resource('assets', AssetController::class);
        Route::resource('licences', LicenceController::class);

        // Module 3 : Écosystème Formations
        Route::resource('catalogue', CatalogueFormationController::class);
        Route::resource('sessions', SessionFormationController::class);

        // Module 4 : Comptabilité de Haut Niveau
        Route::resource('factures', FactureController::class);
        Route::resource('tresorerie', TresorerieController::class)->only(['index', 'store']);
        Route::patch('notes/{id}/statut', [NoteFraisController::class, 'updateStatut'])->name('notes.status');
    });

    // --- ZONE COLLABORATEURS (Employés / Stagiaires) ---
    Route::resource('pointages', PointageController::class)->only(['index', 'store']);
    Route::resource('feuille_temps', FeuilleTempsController::class)->only(['index', 'create', 'store']);
    Route::resource('tickets', TicketMaintenanceController::class)->only(['index', 'create', 'store']);

    // Notes de Frais (Tels des employés, ils créent et suivent, l'admin valide au-dessus)
    Route::resource('notes', NoteFraisController::class)->only(['index', 'create', 'store']);

    // --- ZONE CLIENTS / EXTÉRIEURS ---
    Route::post('inscriptions', [InscriptionController::class, 'store'])->name('inscriptions.store');
    Route::post('evaluations', [InscriptionController::class, 'evaluer'])->name('evaluations.store');
});

require __DIR__.'/auth.php';

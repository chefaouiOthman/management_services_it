<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\User;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StagiaireController extends Controller
{
    /**
     * LISTE DES STAGIAIRES
     */
    public function index()
    {
        // Récupère le stagiaire, son identité et l'identité de son encadrant (employé)
        $stagiaires = Stagiaire::with(['user', 'encadrant.user'])->get();
        return view('stagiaires.index', compact('stagiaires'));
    }

    /**
     * FORMULAIRE DE CREATION
     */
    public function create()
    {
        // Liste des employés pour choisir l'encadrant obligatoire (Page 10 du PDF)
        $employes = Employe::with('user')->get();
        return view('stagiaires.create', compact('employes'));
    }

    /**
     * ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        // Validation stricte
        $request->validate([
            'nom_complet'   => 'required|string|max:150',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'ecole_origine' => 'required|string|max:150',
            'sujet_stage'   => 'required|string',
            'employe_id'    => 'required|exists:employes,id', // L'encadrant doit exister
        ]);

        // Transaction globale pour l'intégrité de l'héritage d'ID
        DB::transaction(function () use ($request) {

            // A. Création de l'identité globale de l'utilisateur
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'est_actif'   => true,
            ]);

            // B. Création du profil stagiaire (Liaison 1-1 via héritage d'ID)
            Stagiaire::create([
                'id'            => $user->id,
                'ecole_origine' => $request->ecole_origine,
                'sujet_stage'   => $request->sujet_stage,
                'employe_id'    => $request->employe_id,
            ]);

            // C. Attribution du rôle de sécurité
            $user->assignRole('Stagiaire');
        });

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès.');
    }
}

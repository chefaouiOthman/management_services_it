<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\User;
use App\Models\Departement;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class EmployeController extends Controller
{
    /**
     * 1. LISTE DES EMPLOYES
     */
    public function index()
    {
        // On récupère l'employé avec ses relations (Identité User, son Département et son Contrat)
        $employes = Employe::with(['user', 'departement', 'contrat'])->get();
        return view('employes.index', compact('employes'));
    }

    /**
     * 2. FORMULAIRE DE CREATION
     */
    public function create()
    {
        // Pour alimenter les listes déroulantes (<select>) du formulaire
        $departements = Departement::all();
        $roles = Role::all();

        return view('employes.create', compact('departements', 'roles'));
    }

    /**
     * 3. ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        // Validation stricte en se basant sur le PDF fourni
        $request->validate([
            // Informations d'authentification (User)
            'nom_complet'     => 'required|string|max:150',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8',

            // Informations spécifiques de l'Employé
            'CIN'             => 'required|string|max:50|unique:employes',
            'date_embauche'   => 'required|date',
            'departement_id'  => 'required|exists:departements,id',

            // Informations du Contrat de travail
            'type_contrat'    => 'required|in:CDI,CDD,Stage,Freelance',
            'date_debut'      => 'required|date',
            'date_fin'        => 'nullable|date|after_or_equal:date_debut',
            'salaire_base'    => 'required|numeric|min:0',
            'heures_hebdo'    => 'required|integer|min:0',

            // Rôle Spatie (Autorisations)
            'role'            => 'required|exists:roles,name',
        ]);

        // Transaction SQL pour garantir que si une table échoue, rien n'est écrit
        DB::transaction(function () use ($request) {

            // A. Insertion de l'utilisateur global
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'est_actif'   => true,
            ]);

            // B. Insertion du profil de l'employé
            $employe = Employe::create([
                'id'             => $user->id, // Héritage d'ID
                'CIN'            => $request->CIN,
                'date_embauche'  => $request->date_embauche,
                'departement_id' => $request->departement_id,
            ]);

            // C. Création du contrat associé à cet employé
            Contrat::create([
                'employe_id'    => $employe->id,
                'type_contrat'  => $request->type_contrat,
                'date_debut'    => $request->date_debut,
                'date_fin'      => $request->date_fin,
                'salaire_base'  => $request->salaire_base,
                'heures_hebdo'  => $request->heures_hebdo,
                'statut'        => 'actif',
            ]);

            // D. Assignation du rôle de sécurité (Spatie)
            $user->assignRole($request->role);
        });

        return redirect()->route('employes.index')->with('success', 'Employé, contrat et droits configurés avec succès !');
    }

    /**
     * 4. SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        // En supprimant l'User, le cascade supprime automatiquement l'employé et son contrat
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('employes.index')->with('success', 'Compte et profil supprimés.');
    }
}

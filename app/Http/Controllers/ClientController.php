<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * LISTE DES CLIENTS
     */
    public function index()
    {
        $clients = Client::with('user')->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * FORMULAIRE DE CREATION
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        // Validation rigoureuse basée sur le dictionnaire de données du PDF
        $request->validate([
            'nom_complet' => 'required|string|max:150',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:8',
            'type_client' => 'required|in:physique,morale',
            'nom_societe' => 'nullable|string|max:150',
            'ice'         => 'nullable|string|max:50',
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

            // B. Création du profil client (Liaison 1-1 via héritage d'ID)
            Client::create([
                'id'           => $user->id,
                'type_client'  => $request->type_client,
                'nom_societe'  => $request->nom_societe,
                'ice'          => $request->ice,
            ]);

            // C. Attribution du rôle de sécurité
            $user->assignRole('Client');
        });

        return redirect()->route('clients.index')->with('success', 'Client configuré avec succès.');
    }
}

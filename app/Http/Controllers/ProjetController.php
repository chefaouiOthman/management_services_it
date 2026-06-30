<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    /**
     * LISTE DES PROJETS
     */
    public function index()
    {
        // On récupère les projets avec le client associé (Liaison Client 0,N -> 1,1 Projet)
        $projets = Projet::with('client')->get();
        return view('projets.index', compact('projets'));
    }

    /**
     * FORMULAIRE DE CREATION
     */
    public function create()
    {
        $clients = Client::with('user')->get();
        return view('projets.create', compact('clients'));
    }

    /**
     * ENREGISTREMENT
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_projet'   => 'required|string|max:150',
            'description'  => 'nullable|string',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
            'statut'       => 'required|in:non_commence,en_cours,termine,en_pause',
            'client_id'    => 'required|exists:clients,id',
        ]);

        Projet::create($request->all());

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }
}

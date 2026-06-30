<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TacheController extends Controller
{
    /**
     * LISTE DES TACHES
     */
    public function index()
    {
        $taches = Tache::with('projets')->get();
        return view('taches.index', compact('taches'));
    }

    /**
     * FORMULAIRE DE CREATION
     */
    public function create()
    {
        $projets = Projet::all();
        return view('taches.create', compact('projets'));
    }

    /**
     * ENREGISTREMENT ET LIAISON AU PROJET (Via Pivot)
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre_tache'  => 'required|string|max:150',
            'priorite'     => 'required|in:basse,moyenne,haute,bloquante',
            'statut_tache' => 'required|in:backlog,en_cours,en_revue,termine',
            'projet_id'    => 'required|exists:projets,id',
        ]);
        DB::transaction(function () use ($request) {
            $tache = Tache::create([
                'titre_tache' => $request->titre_tache,
            ]);
            $tache->projets()->attach($request->projet_id, [
                'statut_tache' => $request->statut_tache,
                'priorite'     => $request->priorite
            ]);
        });

        return redirect()->route('taches.index')->with('success', 'Tâche créée et configurée pour ce projet.');    }
}

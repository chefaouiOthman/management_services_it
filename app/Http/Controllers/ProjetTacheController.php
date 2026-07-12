<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetTacheController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projet-edit'); // Simplification des permissions pour le pivot
    }

    /**
     * Affiche le formulaire pour associer/créer une tâche dans un projet.
     */
    public function create(Projet $projet)
    {
        // On récupère les tâches qui ne sont PAS encore associées à ce projet
        $tachesExistantes = Tache::whereDoesntHave('projets', function ($query) use ($projet) {
            $query->where('projets.id', $projet->id);
        })->get();

        return view('projets.taches.create', compact('projet', 'tachesExistantes'));
    }

    /**
     * Enregistre l'association (ou crée puis associe).
     */
    public function store(Request $request, Projet $projet)
    {
        $request->validate([
            'tache_id'        => 'nullable|exists:taches,id',
            'titre_tache_new' => 'nullable|required_without:tache_id|string|max:150',
            'priorite'        => 'required|in:basse,moyenne,haute,bloquante',
            'statut_tache'    => 'required|in:backlog,en_cours,en_revue,termine',
        ]);

        if (!$request->tache_id && !$request->titre_tache_new) {
            return back()->withErrors(['tache_id' => 'Vous devez sélectionner une tâche ou en créer une nouvelle.'])->withInput();
        }

        DB::transaction(function () use ($request, $projet) {
            $tacheId = $request->tache_id;

            // Si l'utilisateur crée une nouvelle tâche au lieu d'en sélectionner une
            if (empty($tacheId)) {
                $tache = Tache::create([
                    'titre_tache' => $request->titre_tache_new,
                ]);
                $tacheId = $tache->id;
            }

            // Attacher au projet via le pivot
            $projet->taches()->attach($tacheId, [
                'priorite'     => $request->priorite,
                'statut_tache' => $request->statut_tache,
            ]);
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Tâche ajoutée au projet avec succès.');
    }

    /**
     * Affiche le formulaire d'édition pour la relation Projet ↔ Tâche (pivot).
     */
    public function edit(Projet $projet, Tache $tache)
    {
        // On vérifie que la tâche appartient bien au projet, et on récupère les attributs pivot
        $tachePivot = $projet->taches()->where('taches.id', $tache->id)->firstOrFail();

        return view('projets.taches.edit', compact('projet', 'tachePivot'));
    }

    /**
     * Met à jour la relation Projet ↔ Tâche (pivot).
     */
    public function update(Request $request, Projet $projet, Tache $tache)
    {
        $request->validate([
            'priorite'     => 'required|in:basse,moyenne,haute,bloquante',
            'statut_tache' => 'required|in:backlog,en_cours,en_revue,termine',
        ]);

        DB::transaction(function () use ($request, $projet, $tache) {
            $projet->taches()->updateExistingPivot($tache->id, [
                'priorite'     => $request->priorite,
                'statut_tache' => $request->statut_tache,
            ]);
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Paramètres de la tâche mis à jour avec succès.');
    }

    /**
     * Détache la tâche du projet.
     */
    public function destroy(Projet $projet, Tache $tache)
    {
        DB::transaction(function () use ($projet, $tache) {
            $projet->taches()->detach($tache->id);
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Tâche retirée du projet avec succès.');
    }
}

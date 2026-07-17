<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TacheController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tache-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:tache-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tache-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tache-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = Tache::with('projets');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('titre_tache', 'like', "%{$s}%")
                  ->orWhereHas('projets', fn ($p) => $p->where('statut_tache', 'like', "%{$s}%")->orWhere('priorite', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('statut_tache')) {
            $query->whereHas('projets', fn ($p) => $p->where('statut_tache', $request->statut_tache));
        }
        if ($request->filled('priorite')) {
            $query->whereHas('projets', fn ($p) => $p->where('priorite', $request->priorite));
        }
        if ($request->filled('projet_id')) {
            $query->whereHas('projets', fn ($p) => $p->where('projets.id', $request->projet_id));
        }

        $taches = $query->paginate(25)->appends($request->query());
        return view('taches.index', compact('taches'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $projets = Projet::all();
        return view('taches.create', compact('projets'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre_tache'  => 'required|string|max:150',
            'projet_id'    => 'nullable|exists:projets,id',
            'priorite'     => 'nullable|required_with:projet_id|in:basse,moyenne,haute,bloquante',
            'statut_tache' => 'nullable|required_with:projet_id|in:backlog,en_cours,en_revue,termine',
        ]);

        DB::transaction(function () use ($request) {
            $tache = Tache::create([
                'titre_tache' => $request->titre_tache,
            ]);

            if ($request->filled('projet_id')) {
                $tache->projets()->attach($request->projet_id, [
                    'priorite'     => $request->priorite,
                    'statut_tache' => $request->statut_tache,
                ]);
            }
        });

        return redirect()->route('taches.index')->with('success', 'Tâche créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $tache = Tache::with(['projets', 'feuilleTemps'])->findOrFail($id);
        return view('taches.show', compact('tache'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $tache = Tache::with('projets')->findOrFail($id);
        $projets = Projet::all();
        // Pour simplifier l'édition, on s'attend à ce que la tâche soit reliée à un seul projet principal dans l'interface, 
        // même si c'est une relation N:N. On prend le premier projet s'il existe.
        $currentProjet = $tache->projets->first();

        return view('taches.edit', compact('tache', 'projets', 'currentProjet'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $tache = Tache::findOrFail($id);

        $request->validate([
            'titre_tache'  => 'required|string|max:150',
            'projet_id'    => 'nullable|exists:projets,id',
            'priorite'     => 'nullable|required_with:projet_id|in:basse,moyenne,haute,bloquante',
            'statut_tache' => 'nullable|required_with:projet_id|in:backlog,en_cours,en_revue,termine',
        ]);

        DB::transaction(function () use ($request, $tache) {
            $tache->update([
                'titre_tache' => $request->titre_tache,
            ]);

            if ($request->filled('projet_id')) {
                // Remplacer toutes les liaisons de projets par la nouvelle sélection (comportement d'UI classique)
                $tache->projets()->sync([
                    $request->projet_id => [
                        'priorite'     => $request->priorite,
                        'statut_tache' => $request->statut_tache,
                    ]
                ]);
            } else {
                $tache->projets()->detach();
            }
        });

        return redirect()->route('taches.index')->with('success', 'Tâche mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $tache = Tache::findOrFail($id);

        DB::transaction(function () use ($tache) {
            $tache->projets()->detach();
            $tache->feuilleTemps()->detach();
            $tache->delete();
        });

        return redirect()->route('taches.index')->with('success', 'Tâche supprimée avec succès.');
    }
}

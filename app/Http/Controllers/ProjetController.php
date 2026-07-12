<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Client;
use App\Models\Technologie;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjetController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projet-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:projet-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:projet-edit', ['only' => ['edit', 'update', 'updateTacheStatut']]);
        $this->middleware('permission:projet-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $projets = Projet::with(['client.user', 'technologies'])
            ->withCount('taches')
            ->get();
        return view('projets.index', compact('projets'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $clients = Client::with('user')->get();
        $technologies = Technologie::all();
        return view('projets.create', compact('clients', 'technologies'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'        => 'required|exists:clients,user_id',
            'nom_projet'       => 'required|string|max:150',
            'description'      => 'required|string',
            'budget_vendu'     => 'required|numeric|min:0',
            'statut_projet'    => 'required|in:analyse,developpement,recette,deploie,maintenance',
            'technologies'     => 'nullable|array',
            'technologies.*'   => 'exists:technologies,id',
            'new_technologies' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $projet = Projet::create([
                'client_id'     => $request->client_id,
                'nom_projet'    => $request->nom_projet,
                'description'   => $request->description,
                'budget_vendu'  => $request->budget_vendu,
                'statut_projet' => $request->statut_projet,
            ]);

            $techIds = $request->technologies ?? [];

            if ($request->filled('new_technologies')) {
                $newTechs = array_map('trim', explode(',', $request->new_technologies));
                foreach ($newTechs as $techName) {
                    if (!empty($techName)) {
                        $tech = Technologie::firstOrCreate(
                            ['nom_tech' => $techName],
                            ['version' => '1.0'] // Valeur par défaut
                        );
                        if (!in_array($tech->id, $techIds)) {
                            $techIds[] = $tech->id;
                        }
                    }
                }
            }

            if (!empty($techIds)) {
                $projet->technologies()->attach($techIds);
            }
        });

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    /**
     * 4. SHOW — Hub Kanban (Eager Loading anti-N+1 complet)
     */
    public function show($id)
    {
        $projet = Projet::with([
            'client.user',
            'technologies',
            'livrables',
            'feuilleTemps.employe.user',
            'feuilleTemps.taches',
            'taches', // inclut les attributs du pivot (priorite, statut_tache)
        ])->findOrFail($id);

        // Organiser les tâches par colonne Kanban pour Alpine.js
        $tachesParStatut = [
            'backlog'   => $projet->taches->where('pivot.statut_tache', 'backlog')->values(),
            'en_cours'  => $projet->taches->where('pivot.statut_tache', 'en_cours')->values(),
            'en_revue'  => $projet->taches->where('pivot.statut_tache', 'en_revue')->values(),
            'termine'   => $projet->taches->where('pivot.statut_tache', 'termine')->values(),
        ];

        $totalHeures = $projet->feuilleTemps->sum('duree_heures');

        return view('projets.show', compact('projet', 'tachesParStatut', 'totalHeures'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $projet = Projet::with('technologies')->findOrFail($id);
        $clients = Client::with('user')->get();
        $technologies = Technologie::all();
        return view('projets.edit', compact('projet', 'clients', 'technologies'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);

        $request->validate([
            'client_id'        => 'required|exists:clients,user_id',
            'nom_projet'       => 'required|string|max:150',
            'description'      => 'required|string',
            'budget_vendu'     => 'required|numeric|min:0',
            'statut_projet'    => 'required|in:analyse,developpement,recette,deploie,maintenance',
            'technologies'     => 'nullable|array',
            'technologies.*'   => 'exists:technologies,id',
            'new_technologies' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $projet) {
            $projet->update([
                'client_id'     => $request->client_id,
                'nom_projet'    => $request->nom_projet,
                'description'   => $request->description,
                'budget_vendu'  => $request->budget_vendu,
                'statut_projet' => $request->statut_projet,
            ]);

            $techIds = $request->technologies ?? [];

            if ($request->filled('new_technologies')) {
                $newTechs = array_map('trim', explode(',', $request->new_technologies));
                foreach ($newTechs as $techName) {
                    if (!empty($techName)) {
                        $tech = Technologie::firstOrCreate(
                            ['nom_tech' => $techName],
                            ['version' => '1.0']
                        );
                        if (!in_array($tech->id, $techIds)) {
                            $techIds[] = $tech->id;
                        }
                    }
                }
            }

            $projet->technologies()->sync($techIds);
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);

        DB::transaction(function () use ($projet) {
            $projet->technologies()->detach();
            $projet->taches()->detach();
            $projet->delete();
        });

        return redirect()->route('projets.index')->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * KANBAN : Mise à jour du statut d'une tâche sur le pivot (appel Fetch asynchrone)
     */
    public function updateTacheStatut(Request $request, Projet $projet, Tache $tache)
    {
        $request->validate([
            'statut_tache' => 'required|in:backlog,en_cours,en_revue,termine',
        ]);

        // Vérifier que la tâche appartient bien à ce projet
        if (!$projet->taches()->where('taches.id', $tache->id)->exists()) {
            return response()->json(['error' => 'Tâche non trouvée dans ce projet.'], 404);
        }

        DB::transaction(function () use ($projet, $tache, $request) {
            $projet->taches()->updateExistingPivot($tache->id, [
                'statut_tache' => $request->statut_tache,
            ]);
        });

        return response()->json([
            'success'      => true,
            'statut_tache' => $request->statut_tache,
            'message'      => 'Statut mis à jour.',
        ]);
    }
}

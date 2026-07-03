<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Client;
use App\Models\Technologie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjetController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $projets = Projet::with(['client.user', 'technologies'])->get();
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
            'client_id'      => 'required|exists:clients,user_id',
            'nom_projet'     => 'required|string|max:150',
            'description'    => 'required|string',
            'budget_vendu'   => 'required|numeric|min:0',
            'statut_projet'  => 'required|in:analyse,developpement,recette,deploie,maintenance',
            'technologies'   => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);

        DB::transaction(function () use ($request) {
            $projet = Projet::create([
                'client_id'     => $request->client_id,
                'nom_projet'    => $request->nom_projet,
                'description'   => $request->description,
                'budget_vendu'  => $request->budget_vendu,
                'statut_projet' => $request->statut_projet,
            ]);

            if ($request->has('technologies')) {
                $projet->technologies()->attach($request->technologies);
            }
        });

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $projet = Projet::with(['client.user', 'technologies', 'taches', 'livrables', 'feuilleTemps'])->findOrFail($id);
        return view('projets.show', compact('projet'));
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
            'client_id'      => 'required|exists:clients,user_id',
            'nom_projet'     => 'required|string|max:150',
            'description'    => 'required|string',
            'budget_vendu'   => 'required|numeric|min:0',
            'statut_projet'  => 'required|in:analyse,developpement,recette,deploie,maintenance',
            'technologies'   => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);

        DB::transaction(function () use ($request, $projet) {
            $projet->update([
                'client_id'     => $request->client_id,
                'nom_projet'    => $request->nom_projet,
                'description'   => $request->description,
                'budget_vendu'  => $request->budget_vendu,
                'statut_projet' => $request->statut_projet,
            ]);

            if ($request->has('technologies')) {
                $projet->technologies()->sync($request->technologies);
            } else {
                $projet->technologies()->detach();
            }
        });

        return redirect()->route('projets.index')->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);

        DB::transaction(function () use ($projet) {
            $projet->technologies()->detach();
            $projet->delete();
        });

        return redirect()->route('projets.index')->with('success', 'Projet supprimé avec succès.');
    }
}

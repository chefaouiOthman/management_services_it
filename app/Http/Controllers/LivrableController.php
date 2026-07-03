<?php

namespace App\Http\Controllers;

use App\Models\Livrable;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivrableController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:livrable-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:livrable-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:livrable-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:livrable-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $livrables = Livrable::with('projet')->get();
        return view('livrables.index', compact('livrables'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $projets = Projet::all();
        return view('livrables.create', compact('projets'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'projet_id'              => 'required|exists:projets,id',
            'titre_jalon'            => 'required|string|max:150',
            'date_limite_soumission' => 'required|date',
            'statut_client'          => 'required|in:en_attente,rejete_avec_corrections,valide',
        ]);

        DB::transaction(function () use ($request) {
            Livrable::create([
                'projet_id'              => $request->projet_id,
                'titre_jalon'            => $request->titre_jalon,
                'date_limite_soumission' => $request->date_limite_soumission,
                'statut_client'          => $request->statut_client,
            ]);
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $livrable = Livrable::with('projet')->findOrFail($id);
        return view('livrables.show', compact('livrable'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $livrable = Livrable::findOrFail($id);
        $projets = Projet::all();
        return view('livrables.edit', compact('livrable', 'projets'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $livrable = Livrable::findOrFail($id);

        $request->validate([
            'projet_id'              => 'required|exists:projets,id',
            'titre_jalon'            => 'required|string|max:150',
            'date_limite_soumission' => 'required|date',
            'statut_client'          => 'required|in:en_attente,rejete_avec_corrections,valide',
        ]);

        DB::transaction(function () use ($request, $livrable) {
            $livrable->update([
                'projet_id'              => $request->projet_id,
                'titre_jalon'            => $request->titre_jalon,
                'date_limite_soumission' => $request->date_limite_soumission,
                'statut_client'          => $request->statut_client,
            ]);
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $livrable = Livrable::findOrFail($id);

        DB::transaction(function () use ($livrable) {
            $livrable->delete();
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable supprimé avec succès.');
    }
}

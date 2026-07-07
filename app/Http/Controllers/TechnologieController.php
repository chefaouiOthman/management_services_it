<?php

namespace App\Http\Controllers;

use App\Models\Technologie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TechnologieController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:technologie-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:technologie-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:technologie-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:technologie-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $technologies = Technologie::all();
        return view('technologies.index', compact('technologies'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('technologies.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_tech' => 'required|string|max:50|unique:technologies,nom_tech',
            'version'  => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            Technologie::create([
                'nom_tech' => $request->nom_tech,
                'version'  => $request->version,
            ]);
        });

        return redirect()->back()->with('success', 'Technologie ajoutée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $technologie = Technologie::with('projets')->findOrFail($id);
        return view('technologies.show', compact('technologie'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $technologie = Technologie::findOrFail($id);
        return view('technologies.edit', compact('technologie'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $technologie = Technologie::findOrFail($id);

        $request->validate([
            'nom_tech' => ['required', 'string', 'max:50', Rule::unique('technologies')->ignore($technologie->id)],
            'version'  => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request, $technologie) {
            $technologie->update([
                'nom_tech' => $request->nom_tech,
                'version'  => $request->version,
            ]);
        });

        return redirect()->back()->with('success', 'Technologie mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $technologie = Technologie::findOrFail($id);

        DB::transaction(function () use ($technologie) {
            // Nettoyage de la table pivot projet_technologie
            $technologie->projets()->detach();
            $technologie->delete();
        });

        return redirect()->back()->with('success', 'Technologie supprimée avec succès.');
    }
}

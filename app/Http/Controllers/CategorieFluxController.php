<?php

namespace App\Http\Controllers;

use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategorieFluxController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $categories = CategorieFlux::all();
        return view('categorie_flux.index', compact('categories'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('categorie_flux.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle_categorie' => 'required|string|max:100|unique:categorie_flux,libelle_categorie',
            'code_comptable'    => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            CategorieFlux::create([
                'libelle_categorie' => $request->libelle_categorie,
                'code_comptable'    => $request->code_comptable,
            ]);
        });

        return redirect()->route('categorie_flux.index')->with('success', 'Catégorie ajoutée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $categorie = CategorieFlux::findOrFail($id);
        return view('categorie_flux.show', compact('categorie'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $categorie = CategorieFlux::findOrFail($id);
        return view('categorie_flux.edit', compact('categorie'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $categorie = CategorieFlux::findOrFail($id);

        $request->validate([
            'libelle_categorie' => ['required', 'string', 'max:100', Rule::unique('categorie_flux')->ignore($categorie->id)],
            'code_comptable'    => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $categorie) {
            $categorie->update([
                'libelle_categorie' => $request->libelle_categorie,
                'code_comptable'    => $request->code_comptable,
            ]);
        });

        return redirect()->route('categorie_flux.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $categorie = CategorieFlux::findOrFail($id);

        DB::transaction(function () use ($categorie) {
            $categorie->delete();
        });

        return redirect()->route('categorie_flux.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}

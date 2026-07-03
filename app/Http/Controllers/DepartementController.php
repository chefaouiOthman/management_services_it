<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartementController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $departements = Departement::all();
        return view('departements.index', compact('departements'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('departements.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_departement' => 'required|string|max:100|unique:departements,nom_departement',
        ]);

        DB::transaction(function () use ($request) {
            Departement::create([
                'nom_departement' => $request->nom_departement,
            ]);
        });

        return redirect()->route('departements.index')->with('success', 'Département créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $departement = Departement::findOrFail($id);
        return view('departements.show', compact('departement'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $departement = Departement::findOrFail($id);
        return view('departements.edit', compact('departement'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $departement = Departement::findOrFail($id);

        $request->validate([
            'nom_departement' => ['required', 'string', 'max:100', Rule::unique('departements')->ignore($departement->id)],
        ]);

        DB::transaction(function () use ($request, $departement) {
            $departement->update([
                'nom_departement' => $request->nom_departement,
            ]);
        });

        return redirect()->route('departements.index')->with('success', 'Département mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $departement = Departement::findOrFail($id);

        DB::transaction(function () use ($departement) {
            $departement->delete();
        });

        return redirect()->route('departements.index')->with('success', 'Département supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FluxTresorerieController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $flux = FluxTresorerie::with('categorieFlux')->get();
        return view('flux_tresoreries.index', compact('flux'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $categories = CategorieFlux::all();
        return view('flux_tresoreries.create', compact('categories'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'categorie_flux_id' => 'required|exists:categorie_flux,id',
            'type_mouvement'    => 'required|in:entree,sortie',
            'montant_operation' => 'required|numeric|min:0',
            'date_comptable'    => 'required|date_format:Y-m-d H:i:s',
        ]);

        DB::transaction(function () use ($request) {
            FluxTresorerie::create([
                'categorie_flux_id' => $request->categorie_flux_id,
                'type_mouvement'    => $request->type_mouvement,
                'montant_operation' => $request->montant_operation,
                'date_comptable'    => $request->date_comptable,
            ]);
        });

        return redirect()->route('flux_tresoreries.index')->with('success', 'Flux de trésorerie créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $flux = FluxTresorerie::with('categorieFlux')->findOrFail($id);
        return view('flux_tresoreries.show', compact('flux'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $flux = FluxTresorerie::findOrFail($id);
        $categories = CategorieFlux::all();
        return view('flux_tresoreries.edit', compact('flux', 'categories'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $flux = FluxTresorerie::findOrFail($id);

        $request->validate([
            'categorie_flux_id' => 'required|exists:categorie_flux,id',
            'type_mouvement'    => 'required|in:entree,sortie',
            'montant_operation' => 'required|numeric|min:0',
            'date_comptable'    => 'required|date_format:Y-m-d H:i:s',
        ]);

        DB::transaction(function () use ($request, $flux) {
            $flux->update([
                'categorie_flux_id' => $request->categorie_flux_id,
                'type_mouvement'    => $request->type_mouvement,
                'montant_operation' => $request->montant_operation,
                'date_comptable'    => $request->date_comptable,
            ]);
        });

        return redirect()->route('flux_tresoreries.index')->with('success', 'Flux de trésorerie mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $flux = FluxTresorerie::findOrFail($id);

        DB::transaction(function () use ($flux) {
            $flux->delete();
        });

        return redirect()->route('flux_tresoreries.index')->with('success', 'Flux de trésorerie supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SessionFormation;
use App\Models\CatalogueFormation;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionFormationController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $sessions = SessionFormation::with(['catalogueFormation', 'formateurs.user'])->get();
        return view('sessions.index', compact('sessions'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $catalogues = CatalogueFormation::all();
        $employes = Employe::with('user')->get();
        return view('sessions.create', compact('catalogues', 'employes'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
            'date_debut'             => 'required|date',
            'date_fin'               => 'required|date|after_or_equal:date_debut',
            'salle_virtuelle'        => 'nullable|string|max:255',
            'salle_concrete'         => 'nullable|string|max:255',
            'formateurs'             => 'nullable|array',
            'formateurs.*'           => 'exists:employes,user_id',
        ]);

        DB::transaction(function () use ($request) {
            $session = SessionFormation::create([
                'catalogue_formation_id' => $request->catalogue_formation_id,
                'date_debut'             => $request->date_debut,
                'date_fin'               => $request->date_fin,
                'salle_virtuelle'        => $request->salle_virtuelle,
                'salle_concrete'         => $request->salle_concrete,
            ]);

            if ($request->has('formateurs')) {
                $session->formateurs()->attach($request->formateurs);
            }
        });

        return redirect()->route('sessions.index')->with('success', 'Session créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $session = SessionFormation::with(['catalogueFormation', 'formateurs.user', 'inscriptions.user', 'evaluations'])->findOrFail($id);
        return view('sessions.show', compact('session'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $session = SessionFormation::with('formateurs')->findOrFail($id);
        $catalogues = CatalogueFormation::all();
        $employes = Employe::with('user')->get();
        return view('sessions.edit', compact('session', 'catalogues', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $session = SessionFormation::findOrFail($id);

        $request->validate([
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
            'date_debut'             => 'required|date',
            'date_fin'               => 'required|date|after_or_equal:date_debut',
            'salle_virtuelle'        => 'nullable|string|max:255',
            'salle_concrete'         => 'nullable|string|max:255',
            'formateurs'             => 'nullable|array',
            'formateurs.*'           => 'exists:employes,user_id',
        ]);

        DB::transaction(function () use ($request, $session) {
            $session->update([
                'catalogue_formation_id' => $request->catalogue_formation_id,
                'date_debut'             => $request->date_debut,
                'date_fin'               => $request->date_fin,
                'salle_virtuelle'        => $request->salle_virtuelle,
                'salle_concrete'         => $request->salle_concrete,
            ]);

            if ($request->has('formateurs')) {
                $session->formateurs()->sync($request->formateurs);
            } else {
                $session->formateurs()->detach();
            }
        });

        return redirect()->route('sessions.index')->with('success', 'Session mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $session = SessionFormation::findOrFail($id);

        DB::transaction(function () use ($session) {
            $session->formateurs()->detach();
            $session->delete();
        });

        return redirect()->route('sessions.index')->with('success', 'Session supprimée avec succès.');
    }
}

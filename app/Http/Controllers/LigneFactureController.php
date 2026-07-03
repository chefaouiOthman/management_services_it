<?php

namespace App\Http\Controllers;

use App\Models\LigneFacture;
use App\Models\Facture;
use App\Models\FluxTresorerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LigneFactureController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $lignes = LigneFacture::with('facture')->get();
        return view('ligne_factures.index', compact('lignes'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $factures = Facture::all();
        return view('ligne_factures.create', compact('factures'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'facture_id'       => 'required|exists:factures,id',
            'designation'      => 'required|string|max:255',
            'quantite'         => 'required|numeric|min:0',
            'prix_unitaire_ht' => 'required|numeric|min:0',
            'taux_tva'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            LigneFacture::create([
                'facture_id'       => $request->facture_id,
                'designation'      => $request->designation,
                'quantite'         => $request->quantite,
                'prix_unitaire_ht' => $request->prix_unitaire_ht,
                'taux_tva'         => $request->taux_tva,
            ]);

            $this->updateFactureFlux($request->facture_id);
        });

        return redirect()->route('ligne_factures.index')->with('success', 'Ligne ajoutée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $ligne = LigneFacture::with('facture')->findOrFail($id);
        return view('ligne_factures.show', compact('ligne'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $ligne = LigneFacture::findOrFail($id);
        $factures = Facture::all();
        return view('ligne_factures.edit', compact('ligne', 'factures'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $ligne = LigneFacture::findOrFail($id);

        $request->validate([
            'facture_id'       => 'required|exists:factures,id',
            'designation'      => 'required|string|max:255',
            'quantite'         => 'required|numeric|min:0',
            'prix_unitaire_ht' => 'required|numeric|min:0',
            'taux_tva'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $ligne) {
            $old_facture_id = $ligne->facture_id;

            $ligne->update([
                'facture_id'       => $request->facture_id,
                'designation'      => $request->designation,
                'quantite'         => $request->quantite,
                'prix_unitaire_ht' => $request->prix_unitaire_ht,
                'taux_tva'         => $request->taux_tva,
            ]);

            $this->updateFactureFlux($request->facture_id);
            if ($old_facture_id != $request->facture_id) {
                $this->updateFactureFlux($old_facture_id);
            }
        });

        return redirect()->route('ligne_factures.index')->with('success', 'Ligne mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $ligne = LigneFacture::findOrFail($id);

        DB::transaction(function () use ($ligne) {
            $facture_id = $ligne->facture_id;
            $ligne->delete();
            $this->updateFactureFlux($facture_id);
        });

        return redirect()->route('ligne_factures.index')->with('success', 'Ligne supprimée avec succès.');
    }

    /**
     * Recalcule le flux de trésorerie de la facture associée si elle est soldée
     */
    private function updateFactureFlux($facture_id)
    {
        $facture = Facture::with('ligneFactures')->find($facture_id);
        if ($facture && $facture->statut_paiement === 'soldee' && $facture->flux_tresorerie_id) {
            $montant_ttc = $facture->ligneFactures->sum(function($l) {
                return $l->quantite * $l->prix_unitaire_ht * (1 + $l->taux_tva / 100);
            });
            FluxTresorerie::where('id', $facture->flux_tresorerie_id)->update(['montant_operation' => $montant_ttc]);
        }
    }
}

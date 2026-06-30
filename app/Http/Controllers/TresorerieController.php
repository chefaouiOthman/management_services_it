<?php

namespace App\Http\Controllers;

use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;

class TresorerieController extends Controller
{
    /**
     * LE LIVRE DE COMPTABILITÉ GÉNÉRALE
     */
    public function index()
    {
        // Centralise tous les flux en chargeant dynamiquement les liaisons optionnelles
        $flux = FluxTresorerie::with(['categorieFlux', 'facture', 'fichePaie', 'noteDeFrais'])->get();

        // Calcul du solde virtuel de l'entreprise à la volée
        $solde = $flux->sum(function($item) {
            return $item->type_flux === 'entree' ? $item->montant : -$item->montant;
        });

        return view('finance.tresorerie.index', compact('flux', 'solde'));
    }

    /**
     * ENREGISTREMENT MANUEL D'UN FLUX (Ex: Paiement d'un fournisseur ou taxe)
     */
    public function store(Request $request)
    {
        $request->validate([
            'categorie_flux_id' => 'required|exists:categorie_flux,id',
            'type_flux'         => 'required|in:entree,sortie',
            'montant'           => 'required|numeric|min:0.01',
            'date_mouvement'    => 'required|date',
            'commentaire'       => 'nullable|string',
            // Les ID de liaison optionnels selon ce qu'on enregistre
            'facture_id'        => 'nullable|exists:factures,id',
            'fiche_paie_id'     => 'nullable|exists:fiche_paies,id',
            'note_de_frais_id'  => 'nullable|exists:note_de_frais,id',
        ]);

        FluxTresorerie::create($request->all());

        return redirect()->route('tresorerie.index')->with('success', 'Mouvement de trésorerie comptabilisé.');
    }
}

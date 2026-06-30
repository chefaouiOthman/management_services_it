<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    public function index()
    {
        $factures = Facture::with(['client.user', 'lignes'])->get();
        return view('finance.factures.index', compact('factures'));
    }

    public function create()
    {
        $clients = Client::with('user')->get();
        return view('finance.factures.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'numero_facture' => 'required|string|max:100|unique:factures',
            'date_emission'  => 'required|date',
            'date_echeance'  => 'required|date|after_or_equal:date_emission',
            'statut_paiement'=> 'required|in:impaye,paye,en_retard',
            // Validation des lignes (Tableau imbriqué)
            'lignes'         => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.quantite'    => 'required|integer|min:1',
            'lignes.*.prix_unitaire'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalHt = 0;

            // Calcul préalable du montant total basé sur les lignes transmises
            foreach ($request->lignes as $ligne) {
                $totalHt += $ligne['quantite'] * $ligne['prix_unitaire'];
            }

            // 1. Création de la facture parente
            $facture = Facture::create([
                'client_id'      => $request->client_id,
                'numero_facture' => $request->numero_facture,
                'date_emission'  => $request->date_emission,
                'date_echeance'  => $request->date_echeance,
                'montant_total'  => $totalHt,
                'statut_paiement'=> $request->statut_paiement,
            ]);

            // 2. Insertion des lignes enfants rattachées
            foreach ($request->lignes as $ligne) {
                LigneFacture::create([
                    'facture_id'    => $facture->id,
                    'designation'   => $ligne['designation'],
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total'    => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture et lignes de détails enregistrées.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Client;
use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FactureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:facture-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:facture-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:facture-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:facture-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $factures = Facture::with(['client.user', 'fluxTresorerie', 'ligneFactures'])->get();
        return view('factures.index', compact('factures'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $clients = Client::with('user')->get();
        return view('factures.create', compact('clients'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'       => 'required|exists:clients,user_id',
            'num_facture'     => 'required|string|max:50|unique:factures,num_facture',
            'date_emission'   => 'required|date',
            'statut_paiement' => 'required|in:emise,en_retard_paiement,soldee',
            'lignes'          => 'nullable|array',
            'lignes.*.designation'    => 'required_with:lignes|string|max:255',
            'lignes.*.quantite'       => 'required_with:lignes|numeric|min:0',
            'lignes.*.prix_unitaire_ht' => 'required_with:lignes|numeric|min:0',
            'lignes.*.taux_tva'       => 'required_with:lignes|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $facture = Facture::create([
                'client_id'       => $request->client_id,
                'num_facture'     => $request->num_facture,
                'date_emission'   => $request->date_emission,
                'statut_paiement' => $request->statut_paiement,
            ]);

            // Créer les lignes de facture si elles sont fournies
            if ($request->filled('lignes')) {
                foreach ($request->lignes as $ligne) {
                    $facture->ligneFactures()->create([
                        'designation'      => $ligne['designation'],
                        'quantite'         => $ligne['quantite'],
                        'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                        'taux_tva'         => $ligne['taux_tva'],
                    ]);
                }
            }

            // Si soldée dès la création, générer le flux de trésorerie
            if ($request->statut_paiement === 'soldee') {
                $this->syncFluxTresorerie($facture->fresh('ligneFactures'));
            }
        });

        return redirect()->route('flux_tresoreries.index', ['#facturation'])->with('success', 'Facture créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $facture = Facture::with(['client.user', 'fluxTresorerie', 'ligneFactures'])->findOrFail($id);
        return view('factures.show', compact('facture'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $facture = Facture::findOrFail($id);
        $clients = Client::with('user')->get();
        return view('factures.edit', compact('facture', 'clients'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);

        $request->validate([
            'client_id'      => 'required|exists:clients,user_id',
            'num_facture'    => ['required', 'string', 'max:50', Rule::unique('factures')->ignore($facture->id)],
            'date_emission'  => 'required|date',
            'statut_paiement'=> 'required|in:emise,en_retard_paiement,soldee',
        ]);

        DB::transaction(function () use ($request, $facture) {
            $facture->update([
                'client_id'      => $request->client_id,
                'num_facture'    => $request->num_facture,
                'date_emission'  => $request->date_emission,
                'statut_paiement'=> $request->statut_paiement,
            ]);

            // Synchronisation Financière
            if ($facture->statut_paiement === 'soldee') {
                $this->syncFluxTresorerie($facture);
            } else {
                // Si la facture n'est plus soldée, on peut supprimer le flux
                if ($facture->flux_tresorerie_id) {
                    $flux_id = $facture->flux_tresorerie_id;
                    $facture->update(['flux_tresorerie_id' => null]);
                    FluxTresorerie::where('id', $flux_id)->delete();
                }
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);

        DB::transaction(function () use ($facture) {
            $flux_id = $facture->flux_tresorerie_id;
            
            $facture->ligneFactures()->delete();
            $facture->delete();

            // Supprimer le flux de trésorerie associé
            if ($flux_id) {
                FluxTresorerie::where('id', $flux_id)->delete();
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Logique métier interne : Générer/Mettre à jour le Flux de Trésorerie
     */
    private function syncFluxTresorerie(Facture $facture)
    {
        $montant_ttc = $facture->ligneFactures->sum(function($ligne) {
            return $ligne->quantite * $ligne->prix_unitaire_ht * (1 + $ligne->taux_tva / 100);
        });

        $categorie = CategorieFlux::firstOrCreate(
            ['libelle_categorie' => 'Vente / Facture Client'],
            ['code_comptable'    => '701']
        );

        if ($facture->flux_tresorerie_id) {
            FluxTresorerie::where('id', $facture->flux_tresorerie_id)->update([
                'montant_operation' => $montant_ttc,
                'date_comptable'    => now(),
            ]);
        } else {
            $flux = FluxTresorerie::create([
                'categorie_flux_id' => $categorie->id,
                'type_mouvement'    => 'entree',
                'montant_operation' => $montant_ttc,
                'date_comptable'    => now(),
            ]);
            $facture->update(['flux_tresorerie_id' => $flux->id]);
        }
    }

    /**
     * 8. UPDATE STATUT (Asynchrone Alpine Fetch)
     */
    public function updateStatut(Request $request, Facture $facture)
    {
        $request->validate([
            'statut_paiement' => 'required|in:emise,en_retard_paiement,soldee',
        ]);

        if (!auth()->user()->hasPermissionTo('facture-edit')) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        DB::transaction(function () use ($request, $facture) {
            $facture->update([
                'statut_paiement' => $request->statut_paiement,
            ]);

            if ($facture->statut_paiement === 'soldee') {
                $this->syncFluxTresorerie($facture);
            } else {
                if ($facture->flux_tresorerie_id) {
                    $flux_id = $facture->flux_tresorerie_id;
                    $facture->update(['flux_tresorerie_id' => null]);
                    FluxTresorerie::where('id', $flux_id)->delete();
                }
            }
        });

        return response()->json([
            'success' => true,
            'statut_paiement' => $request->statut_paiement,
            'message' => 'Statut de la facture mis à jour.',
        ]);
    }
}

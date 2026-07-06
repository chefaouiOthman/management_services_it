<?php

namespace App\Http\Controllers;

use App\Models\FichePaie;
use App\Models\Employe;
use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FichePaieController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:fiche-paie-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:fiche-paie-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:fiche-paie-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:fiche-paie-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        // Redirection vers le hub central financier
        return redirect()->route('flux_tresoreries.index')->withFragment('#rh');
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $employes = Employe::with('user')->get();
        return view('fiche_paies.create', compact('employes'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id'  => 'required|exists:employes,user_id',
            'mois_annee'  => 'required|string|max:7',
            'net_a_payer' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $fiche = FichePaie::create([
                'employe_id'  => $request->employe_id,
                'mois_annee'  => $request->mois_annee,
                'net_a_payer' => $request->net_a_payer,
            ]);

            $this->syncFluxTresorerie($fiche);
        });

        return redirect()->route('fiche_paies.index')->with('success', 'Fiche de paie générée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $fiche = FichePaie::with(['employe.user', 'fluxTresorerie'])->findOrFail($id);
        return view('fiche_paies.show', compact('fiche'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $fiche = FichePaie::findOrFail($id);
        $employes = Employe::with('user')->get();
        return view('fiche_paies.edit', compact('fiche', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $fiche = FichePaie::findOrFail($id);

        $request->validate([
            'employe_id'  => 'required|exists:employes,user_id',
            'mois_annee'  => 'required|string|max:7',
            'net_a_payer' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $fiche) {
            $fiche->update([
                'employe_id'  => $request->employe_id,
                'mois_annee'  => $request->mois_annee,
                'net_a_payer' => $request->net_a_payer,
            ]);

            $this->syncFluxTresorerie($fiche);
        });

        return redirect()->route('fiche_paies.index')->with('success', 'Fiche de paie mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $fiche = FichePaie::findOrFail($id);

        DB::transaction(function () use ($fiche) {
            $flux_id = $fiche->flux_tresorerie_id;
            $fiche->delete();

            if ($flux_id) {
                FluxTresorerie::where('id', $flux_id)->delete();
            }
        });

        return redirect()->route('fiche_paies.index')->with('success', 'Fiche de paie supprimée avec succès.');
    }

    /**
     * Synchronisation Financière
     */
    private function syncFluxTresorerie(FichePaie $fiche)
    {
        $categorie = CategorieFlux::firstOrCreate(
            ['libelle_categorie' => 'Salaires & Paie'],
            ['code_comptable'    => '641']
        );

        if ($fiche->flux_tresorerie_id) {
            FluxTresorerie::where('id', $fiche->flux_tresorerie_id)->update([
                'montant_operation' => $fiche->net_a_payer,
                'date_comptable'    => now(),
            ]);
        } else {
            $flux = FluxTresorerie::create([
                'categorie_flux_id' => $categorie->id,
                'type_mouvement'    => 'sortie',
                'montant_operation' => $fiche->net_a_payer,
                'date_comptable'    => now(),
            ]);
            $fiche->update(['flux_tresorerie_id' => $flux->id]);
        }
    }

    /**
     * 8. PAYER (Asynchrone Alpine Fetch)
     */
    public function payer(Request $request, FichePaie $fiche)
    {
        if (!auth()->user()->hasPermissionTo('fiche-paie-edit')) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        DB::transaction(function () use ($fiche) {
            // Dans ce scénario métier, la paie est émise puis payée
            // On s'assure qu'elle n'est pas déjà payée (id de flux existant)
            // Bien que dans la logique de store() elle crée un flux automatiquement, 
            // la DAF peut valider le paiement ici si on considère qu'une Fiche a un statut "Payée"
            // Pour l'instant, le store() crée directement le flux, donc `payer()` pourrait juste mettre à jour la date comptable du flux.
            
            if ($fiche->flux_tresorerie_id) {
                FluxTresorerie::where('id', $fiche->flux_tresorerie_id)->update([
                    'date_comptable' => now(),
                ]);
            } else {
                $this->syncFluxTresorerie($fiche);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Fiche de paie payée (Flux mis à jour).',
        ]);
    }
}

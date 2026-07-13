<?php

namespace App\Http\Controllers;

use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FluxTresorerieController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Employe_Standard')) {
                abort(403, 'Accès interdit.');
            }
            return $next($request);
        }, ['only' => ['index', 'show']]);

        $this->middleware('permission:flux-tresorerie-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:flux-tresorerie-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:flux-tresorerie-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:flux-tresorerie-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        // 1. Charger les flux pour le Grand Livre
        $flux = FluxTresorerie::with(['categorieFlux', 'facture', 'fichePaie', 'noteDeFrais'])->orderByDesc('date_comptable')->get();
        
        // 2. Charger les données pour l'onglet Facturation
        $factures = \App\Models\Facture::with(['client.user', 'ligneFactures'])->orderByDesc('date_emission')->get();
        
        // 3. Charger les données pour l'onglet Masse Salariale & Frais
        $fiches = \App\Models\FichePaie::with('employe.user')->orderByDesc('created_at')->get();
        $notes = \App\Models\NoteDeFrais::with('employe.user')->orderByDesc('created_at')->get();

        return view('flux_tresoreries.index', compact('flux', 'factures', 'fiches', 'notes'));
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
        $flux = FluxTresorerie::with(['facture', 'fichePaie', 'noteDeFrais'])->findOrFail($id);

        if ($flux->facture) {
            return redirect()->route('factures.edit', $flux->facture->id);
        } elseif ($flux->fichePaie) {
            return redirect()->route('fiche_paies.edit', $flux->fichePaie->id);
        } elseif ($flux->noteDeFrais) {
            return redirect()->route('note_de_frais.edit', $flux->noteDeFrais->id);
        }

        // Flux manuel sans entité source liée — redirection informative
        return redirect()->route('flux_tresoreries.index')
            ->with('info', 'Ce flux n\'est lié à aucune facture, fiche de paie ou note de frais modifiable.');
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

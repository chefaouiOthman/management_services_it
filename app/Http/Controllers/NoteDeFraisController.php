<?php

namespace App\Http\Controllers;

use App\Models\NoteDeFrais;
use App\Models\Employe;
use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NoteDeFraisController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $query = NoteDeFrais::with(['employe.user', 'fluxTresorerie']);
        
        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance')) {
            $query->where('employe_id', Auth::id());
        }

        $notes = $query->get();
        return view('note_de_frais.index', compact('notes'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $employes = (Auth::user()->hasRole('Admin') || Auth::user()->hasPermissionTo('manage-finance')) ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
        return view('note_de_frais.create', compact('employes'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id'           => 'required|exists:employes,user_id',
            'motif_depense'        => 'required|string|max:255',
            'montant_ttc'          => 'required|numeric|min:0',
            'justificatif_path'    => 'required|string|max:255',
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
        ]);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance') && $request->employe_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($request) {
            $note = NoteDeFrais::create([
                'employe_id'           => $request->employe_id,
                'motif_depense'        => $request->motif_depense,
                'montant_ttc'          => $request->montant_ttc,
                'justificatif_path'    => $request->justificatif_path,
                'statut_remboursement' => $request->statut_remboursement,
            ]);

            if ($note->statut_remboursement === 'rembourse') {
                $this->syncFluxTresorerie($note);
            }
        });

        return redirect()->route('note_de_frais.index')->with('success', 'Note de frais créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $note = NoteDeFrais::with(['employe.user', 'fluxTresorerie'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        return view('note_de_frais.show', compact('note'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $note = NoteDeFrais::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        $employes = (Auth::user()->hasRole('Admin') || Auth::user()->hasPermissionTo('manage-finance')) ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
        return view('note_de_frais.edit', compact('note', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $note = NoteDeFrais::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'employe_id'           => 'required|exists:employes,user_id',
            'motif_depense'        => 'required|string|max:255',
            'montant_ttc'          => 'required|numeric|min:0',
            'justificatif_path'    => 'required|string|max:255',
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
        ]);

        DB::transaction(function () use ($request, $note) {
            $note->update([
                'employe_id'           => $request->employe_id,
                'motif_depense'        => $request->motif_depense,
                'montant_ttc'          => $request->montant_ttc,
                'justificatif_path'    => $request->justificatif_path,
                'statut_remboursement' => $request->statut_remboursement,
            ]);

            if ($note->statut_remboursement === 'rembourse') {
                $this->syncFluxTresorerie($note);
            } else {
                if ($note->flux_tresorerie_id) {
                    $flux_id = $note->flux_tresorerie_id;
                    $note->update(['flux_tresorerie_id' => null]);
                    FluxTresorerie::where('id', $flux_id)->delete();
                }
            }
        });

        return redirect()->route('note_de_frais.index')->with('success', 'Note de frais mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $note = NoteDeFrais::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-finance') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($note) {
            $flux_id = $note->flux_tresorerie_id;
            $note->delete();

            if ($flux_id) {
                FluxTresorerie::where('id', $flux_id)->delete();
            }
        });

        return redirect()->route('note_de_frais.index')->with('success', 'Note de frais supprimée avec succès.');
    }

    /**
     * Synchronisation Financière
     */
    private function syncFluxTresorerie(NoteDeFrais $note)
    {
        $categorie = CategorieFlux::firstOrCreate(
            ['libelle_categorie' => 'Notes de frais'],
            ['code_comptable'    => '625']
        );

        if ($note->flux_tresorerie_id) {
            FluxTresorerie::where('id', $note->flux_tresorerie_id)->update([
                'montant_operation' => $note->montant_ttc,
                'date_comptable'    => now(),
            ]);
        } else {
            $flux = FluxTresorerie::create([
                'categorie_flux_id' => $categorie->id,
                'type_mouvement'    => 'sortie',
                'montant_operation' => $note->montant_ttc,
                'date_comptable'    => now(),
            ]);
            $note->update(['flux_tresorerie_id' => $flux->id]);
        }
    }
}

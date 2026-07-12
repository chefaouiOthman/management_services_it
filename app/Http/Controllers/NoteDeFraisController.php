<?php

namespace App\Http\Controllers;

use App\Models\NoteDeFrais;
use App\Models\Employe;
use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteDeFraisController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:note-de-frais-view', ['only' => ['index', 'show', 'download']]);
        $this->middleware('permission:note-de-frais-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:note-de-frais-edit', ['only' => ['edit', 'update', 'updateStatut']]);
        $this->middleware('permission:note-de-frais-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $query = NoteDeFrais::with(['employe.user', 'fluxTresorerie']);
        
        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view')) {
            $query->where('employe_id', Auth::id());
        }

        // Redirection vers le hub central financier
        return redirect()->route('flux_tresoreries.index')->withFragment('#rh');
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $employes = (Auth::user()->hasRole('Admin') || Auth::user()->hasPermissionTo('flux-tresorerie-view')) ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
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
            'justificatif_fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
            'categorie_flux_id'    => 'nullable|exists:categorie_flux,id',
            'new_categorie_flux'   => 'nullable|string|max:100',
        ]);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $request->employe_id != Auth::id()) {
            abort(403);
        }

        $path = $request->file('justificatif_fichier')->store('notes_de_frais', 'private');

        DB::transaction(function () use ($request, $path) {
            $note = NoteDeFrais::create([
                'employe_id'           => $request->employe_id,
                'motif_depense'        => $request->motif_depense,
                'montant_ttc'          => $request->montant_ttc,
                'justificatif_path'    => $path,
                'statut_remboursement' => $request->statut_remboursement,
            ]);

            if ($note->statut_remboursement === 'rembourse') {
                $categorieId = $this->getOrCreateCategorie($request);
                $this->syncFluxTresorerie($note, $categorieId);
            }
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Note de frais créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $note = NoteDeFrais::with(['employe.user', 'fluxTresorerie'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
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

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        $employes = (Auth::user()->hasRole('Admin') || Auth::user()->hasPermissionTo('flux-tresorerie-view')) ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
        return view('note_de_frais.edit', compact('note', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $note = NoteDeFrais::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'employe_id'           => 'required|exists:employes,user_id',
            'motif_depense'        => 'required|string|max:255',
            'montant_ttc'          => 'required|numeric|min:0',
            'justificatif_fichier' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
            'categorie_flux_id'    => 'nullable|exists:categorie_flux,id',
            'new_categorie_flux'   => 'nullable|string|max:100',
        ]);

        $path = $note->justificatif_path;
        if ($request->hasFile('justificatif_fichier')) {
            if (Storage::disk('private')->exists($path)) {
                Storage::disk('private')->delete($path);
            }
            $path = $request->file('justificatif_fichier')->store('notes_de_frais', 'private');
        }

        DB::transaction(function () use ($request, $note, $path) {
            $note->update([
                'employe_id'           => $request->employe_id,
                'motif_depense'        => $request->motif_depense,
                'montant_ttc'          => $request->montant_ttc,
                'justificatif_path'    => $path,
                'statut_remboursement' => $request->statut_remboursement,
            ]);

            if ($note->statut_remboursement === 'rembourse') {
                $categorieId = $this->getOrCreateCategorie($request);
                $this->syncFluxTresorerie($note, $categorieId);
            } else {
                if ($note->flux_tresorerie_id) {
                    $flux_id = $note->flux_tresorerie_id;
                    $note->update(['flux_tresorerie_id' => null]);
                    FluxTresorerie::where('id', $flux_id)->delete();
                }
            }
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Note de frais mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $note = NoteDeFrais::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($note) {
            $flux_id = $note->flux_tresorerie_id;
            
            if (Storage::disk('private')->exists($note->justificatif_path)) {
                Storage::disk('private')->delete($note->justificatif_path);
            }
            
            $note->delete();

            if ($flux_id) {
                FluxTresorerie::where('id', $flux_id)->delete();
            }
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Note de frais supprimée avec succès.');
    }

    /**
     * 8. DOWNLOAD (Téléchargement sécurisé Private Storage)
     */
    public function download(NoteDeFrais $note)
    {
        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('private')->exists($note->justificatif_path)) {
            abort(404, 'Le justificatif est introuvable.');
        }

        return Storage::disk('private')->download($note->justificatif_path);
    }

    /**
     * 9. UPDATE STATUT (Asynchrone Alpine Fetch)
     */
    public function updateStatut(Request $request, NoteDeFrais $note)
    {
        $request->validate([
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
        ]);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('note-de-frais-edit')) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        DB::transaction(function () use ($request, $note) {
            $note->update([
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

        return response()->json([
            'success' => true,
            'statut_remboursement' => $request->statut_remboursement,
            'message' => 'Statut de la note de frais mis à jour.',
        ]);
    }

    /**
     * Synchronisation Financière
     */
    private function syncFluxTresorerie(NoteDeFrais $note, $categorieId = null)
    {
        if (!$categorieId) {
            $categorie = CategorieFlux::firstOrCreate(
                ['libelle_categorie' => 'Notes de frais'],
                ['code_comptable'    => '625']
            );
            $categorieId = $categorie->id;
        }

        if ($note->flux_tresorerie_id) {
            FluxTresorerie::where('id', $note->flux_tresorerie_id)->update([
                'categorie_flux_id' => $categorieId,
                'montant_operation' => $note->montant_ttc,
                'date_comptable'    => now(),
            ]);
        } else {
            $flux = FluxTresorerie::create([
                'categorie_flux_id' => $categorieId,
                'type_mouvement'    => 'sortie',
                'montant_operation' => $note->montant_ttc,
                'date_comptable'    => now(),
            ]);
            $note->update(['flux_tresorerie_id' => $flux->id]);
        }
    }

    private function getOrCreateCategorie(Request $request)
    {
        if ($request->filled('categorie_flux_id')) {
            return $request->categorie_flux_id;
        }

        if ($request->filled('new_categorie_flux')) {
            $categorie = CategorieFlux::firstOrCreate(
                ['libelle_categorie' => $request->new_categorie_flux],
                ['code_comptable' => null]
            );
            return $categorie->id;
        }

        return null;
    }
}

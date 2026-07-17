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
use App\Http\Controllers\Traits\FilterSuperAdmin;

class NoteDeFraisController extends Controller
{
    use FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:note-de-frais-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:note-de-frais-edit', ['only' => ['edit', 'update', 'updateStatut']]);
        $this->middleware('permission:note-de-frais-delete', ['only' => ['destroy']]);
    }

    private function isStagiaireOuClient(): bool
    {
        return Auth::user()->hasAnyRole(['Stagiaire', 'Client']);
    }

    private function isEmployeStandard(): bool
    {
        return Auth::user()->hasRole('Employe_Standard');
    }

    private function isAdmin(): bool
    {
        return Auth::user()->hasRole('Admin');
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()->hasRole('Super Admin');
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        if ($this->isStagiaireOuClient()) {
            abort(403, 'Accès interdit.');
        }

        if ($this->isSuperAdmin()) {
            $query = NoteDeFrais::with('employe.user');
        } elseif ($this->isAdmin()) {
            $query = NoteDeFrais::with('employe.user')->whereDoesntHave('employe.user.roles', fn ($q) => $q->where('name', 'Super Admin'));
        } else {
            $query = NoteDeFrais::with('employe.user')->where('employe_id', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('motif_depense', 'like', "%{$s}%")
                  ->orWhereHas('employe.user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhere('statut_remboursement', 'like', "%{$s}%");
            });
        }
        if ($request->filled('statut_remboursement')) {
            $query->where('statut_remboursement', $request->statut_remboursement);
        }
        if ($request->filled('employe_id') && !$this->isEmployeStandard()) {
            $query->where('employe_id', $request->employe_id);
        }

        $notes = $query->orderByDesc('created_at')->paginate(25)->appends($request->query());
        return view('note_de_frais.index', compact('notes'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        if ($this->isStagiaireOuClient()) {
            abort(403, 'Accès interdit.');
        }

        $employes = ($this->isAdmin() || Auth::user()->hasPermissionTo('flux-tresorerie-view'))
            ? $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get()
            : Employe::where('user_id', Auth::id())->get();

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

        if (!$this->isAdmin() && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $request->employe_id != Auth::id()) {
            abort(403);
        }

        $this->validateNotSuperAdminTarget($request, 'employe_id');

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

        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return redirect()->route('flux_tresoreries.index')->with('success', 'Note de frais créée avec succès.');
        }

        return redirect()->route('note_de_frais.index')->with('success', 'Votre note de frais a été enregistrée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $note = NoteDeFrais::with(['employe.user', 'fluxTresorerie'])->findOrFail($id);

        if ($this->isStagiaireOuClient()) {
            abort(403, 'Accès interdit.');
        }

        if (!$this->isAdmin() && !$this->isSuperAdmin() && !Auth::user()->hasPermissionTo('flux-tresorerie-view') && $note->employe_id != Auth::id()) {
            abort(403);
        }

        if ($this->isAdmin() && $note->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        return view('note_de_frais.show', compact('note'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $note = NoteDeFrais::findOrFail($id);

        if ($this->isAdmin() && $note->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        $employes = $this->isAdmin()
            ? $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get()
            : Employe::with('user')->get();

        return view('note_de_frais.edit', compact('note', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $note = NoteDeFrais::findOrFail($id);

        if ($this->isAdmin() && $note->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
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

        $this->validateNotSuperAdminTarget($request, 'employe_id');

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

        return redirect()->route('flux_tresoreries.index')->withFragment('#notes-frais')->with('success', 'Note de frais mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $note = NoteDeFrais::findOrFail($id);

        if ($this->isAdmin() && $note->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
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

        return redirect()->route('flux_tresoreries.index')->withFragment('#notes-frais')->with('success', 'Note de frais supprimée avec succès.');
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
        if (!$this->isAdmin() && !$this->isSuperAdmin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($this->isAdmin() && $note->employe?->user?->hasRole('Super Admin')) {
            return response()->json(['error' => 'Action non autorisée sur un Super Administrateur.'], 403);
        }

        $request->validate([
            'statut_remboursement' => 'required|in:soumis,approuve_manager,rejete,rembourse',
        ]);

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

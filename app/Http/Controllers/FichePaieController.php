<?php

namespace App\Http\Controllers;

use App\Models\FichePaie;
use App\Models\Employe;
use App\Models\FluxTresorerie;
use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class FichePaieController extends Controller
{
    use FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:fiche-paie-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:fiche-paie-edit', ['only' => ['edit', 'update', 'payer']]);
        $this->middleware('permission:fiche-paie-delete', ['only' => ['destroy']]);
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
            $query = FichePaie::with('employe.user');
        } elseif ($this->isAdmin()) {
            $query = FichePaie::with('employe.user')->whereDoesntHave('employe.user.roles', fn ($q) => $q->where('name', 'Super Admin'));
        } else {
            $query = FichePaie::with('employe.user')->where('employe_id', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('mois_annee', 'like', "%{$s}%")
                  ->orWhereHas('employe.user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('employe_id') && !$this->isEmployeStandard()) {
            $query->where('employe_id', $request->employe_id);
        }
        if ($request->filled('mois_annee')) {
            $query->where('mois_annee', $request->mois_annee);
        }

        $fiches = $query->orderByDesc('created_at')->paginate(25)->appends($request->query());
        return view('fiche_paies.index', compact('fiches'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $employes = $this->isAdmin()
            ? $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get()
            : Employe::with('user')->get();

        return view('fiche_paies.create', compact('employes'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $request->validate([
            'employe_id'  => 'required|exists:employes,user_id',
            'mois_annee'  => 'required|string|max:7',
            'net_a_payer' => 'required|numeric|min:0',
            'categorie_flux_id' => 'nullable|exists:categorie_flux,id',
            'new_categorie_flux' => 'nullable|string|max:100',
        ]);

        $this->validateNotSuperAdminTarget($request, 'employe_id');

        DB::transaction(function () use ($request) {
            $fiche = FichePaie::create([
                'employe_id'  => $request->employe_id,
                'mois_annee'  => $request->mois_annee,
                'net_a_payer' => $request->net_a_payer,
            ]);

            $categorieId = $this->getOrCreateCategorie($request);
            $this->syncFluxTresorerie($fiche, $categorieId);
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Fiche de paie générée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $fiche = FichePaie::with(['employe.user', 'fluxTresorerie'])->findOrFail($id);

        if ($this->isStagiaireOuClient()) {
            abort(403, 'Accès interdit.');
        }

        if ($this->isEmployeStandard() && $fiche->employe_id != Auth::id()) {
            abort(403);
        }

        if ($this->isAdmin() && $fiche->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        return view('fiche_paies.show', compact('fiche'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $fiche = FichePaie::findOrFail($id);

        if ($this->isAdmin() && $fiche->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        $employes = $this->isAdmin()
            ? $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get()
            : Employe::with('user')->get();

        return view('fiche_paies.edit', compact('fiche', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $fiche = FichePaie::findOrFail($id);

        if ($this->isAdmin() && $fiche->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        $request->validate([
            'employe_id'  => 'required|exists:employes,user_id',
            'mois_annee'  => 'required|string|max:7',
            'net_a_payer' => 'required|numeric|min:0',
            'categorie_flux_id' => 'nullable|exists:categorie_flux,id',
            'new_categorie_flux' => 'nullable|string|max:100',
        ]);

        $this->validateNotSuperAdminTarget($request, 'employe_id');

        DB::transaction(function () use ($request, $fiche) {
            $fiche->update([
                'employe_id'  => $request->employe_id,
                'mois_annee'  => $request->mois_annee,
                'net_a_payer' => $request->net_a_payer,
            ]);

            $categorieId = $this->getOrCreateCategorie($request);
            $this->syncFluxTresorerie($fiche, $categorieId);
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Fiche de paie mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            abort(403, 'Accès interdit.');
        }

        $fiche = FichePaie::findOrFail($id);

        if ($this->isAdmin() && $fiche->employe?->user?->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }

        DB::transaction(function () use ($fiche) {
            $flux_id = $fiche->flux_tresorerie_id;
            $fiche->delete();

            if ($flux_id) {
                FluxTresorerie::where('id', $flux_id)->delete();
            }
        });

        return redirect()->route('flux_tresoreries.index')->withFragment('#rh')->with('success', 'Fiche de paie supprimée avec succès.');
    }

    /**
     * 8. PAYER (Asynchrone Alpine Fetch)
     */
    public function payer(Request $request, FichePaie $fiche)
    {
        if ($this->isStagiaireOuClient() || $this->isEmployeStandard()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($this->isAdmin() && $fiche->employe?->user?->hasRole('Super Admin')) {
            return response()->json(['error' => 'Action non autorisée sur un Super Administrateur.'], 403);
        }

        DB::transaction(function () use ($fiche) {
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

    /**
     * Synchronisation Financière
     */
    private function syncFluxTresorerie(FichePaie $fiche, $categorieId = null)
    {
        if (!$categorieId) {
            $categorie = CategorieFlux::firstOrCreate(
                ['libelle_categorie' => 'Salaires & Paie'],
                ['code_comptable'    => '641']
            );
            $categorieId = $categorie->id;
        }

        if ($fiche->flux_tresorerie_id) {
            FluxTresorerie::where('id', $fiche->flux_tresorerie_id)->update([
                'categorie_flux_id' => $categorieId,
                'montant_operation' => $fiche->net_a_payer,
                'date_comptable'    => now(),
            ]);
        } else {
            $flux = FluxTresorerie::create([
                'categorie_flux_id' => $categorieId,
                'type_mouvement'    => 'sortie',
                'montant_operation' => $fiche->net_a_payer,
                'date_comptable'    => now(),
            ]);
            $fiche->update(['flux_tresorerie_id' => $flux->id]);
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

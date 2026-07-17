<?php

namespace App\Http\Controllers;

use App\Models\FeuilleTemps;
use App\Models\Employe;
use App\Models\Projet;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class FeuilleTempsController extends Controller
{
    use FilterSuperAdmin;
    public function __construct()
    {
        $this->middleware('permission:feuille-temps-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:feuille-temps-create', ['only' => ['create', 'store', 'selectProject']]);
        $this->middleware('permission:feuille-temps-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:feuille-temps-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = FeuilleTemps::with(['employe.user', 'projet', 'taches']);

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('employe_id', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('employe.user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhereHas('projet', fn ($p) => $p->where('nom_projet', 'like', "%{$s}%"))
                  ->orWhere('commentaire', 'like', "%{$s}%");
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }

        $feuilles = $query->paginate(25)->appends($request->query());
        $projets = Projet::all();
        return view('feuille_temps.index', compact('feuilles', 'projets'));
    }

    /**
     * 1b. SELECT PROJECT
     */
    public function selectProject(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
        ]);

        return redirect()->route('projets.feuille_temps.create', $request->projet_id);
    }

    /**
     * 2. CREATE
     */
    public function create(Projet $projet)
    {
        $employes = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get() : Employe::where('user_id', Auth::id())->get();
        // Les tâches affichées seront uniquement celles de ce projet
        $taches = $projet->taches;
        
        return view('feuille_temps.create', compact('employes', 'projet', 'taches'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request, Projet $projet)
    {
        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'date_effort'  => 'required|date',
            'duree_heures' => 'required|numeric|min:0.5|max:24',
            'commentaire'  => 'required|string|min:5',
            'taches'       => 'nullable|array',
            'taches.*'     => 'exists:taches,id',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->employe_id != Auth::id()) {
            abort(403, 'Vous ne pouvez pas créer de feuille de temps pour un autre employé.');
        }

        $this->validateNotSuperAdminTarget($request, 'employe_id');

        DB::transaction(function () use ($request, $projet) {
            $feuille = FeuilleTemps::create([
                'employe_id'   => $request->employe_id,
                'projet_id'    => $projet->id,
                'date_effort'  => $request->date_effort,
                'duree_heures' => $request->duree_heures,
                'commentaire'  => $request->commentaire,
                'created_by'   => Auth::id(),
            ]);

            if ($request->has('taches')) {
                $feuille->taches()->attach($request->taches);
            }
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Feuille de temps créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $feuille = FeuilleTemps::with(['employe.user', 'projet', 'taches'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $feuille->employe_id != Auth::id()) {
            abort(403);
        }

        return view('feuille_temps.show', compact('feuille'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $feuille = FeuilleTemps::with('taches')->findOrFail($id);

        if (Auth::user()->hasRole('Super Admin')) {
            // Accès total
        } elseif (Auth::user()->hasRole('Admin')) {
            if ($feuille->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez modifier que les feuilles de temps que vous avez créées.');
            }
        } else {
            abort(403);
        }

        $employes = $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get();
        $projets = Projet::all();
        $taches = Tache::all();
        
        return view('feuille_temps.edit', compact('feuille', 'employes', 'projets', 'taches'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $feuille = FeuilleTemps::findOrFail($id);

        if (Auth::user()->hasRole('Super Admin')) {
            // Accès total
        } elseif (Auth::user()->hasRole('Admin')) {
            if ($feuille->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez modifier que les feuilles de temps que vous avez créées.');
            }
        } else {
            abort(403);
        }

        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'projet_id'    => 'required|exists:projets,id',
            'date_effort'  => 'required|date',
            'duree_heures' => 'required|numeric|min:0.5|max:24',
            'commentaire'  => 'required|string|min:5',
            'taches'       => 'nullable|array',
            'taches.*'     => 'exists:taches,id',
        ]);

        $this->validateNotSuperAdminTarget($request, 'employe_id');

        DB::transaction(function () use ($request, $feuille) {
            $feuille->update([
                'employe_id'   => $request->employe_id,
                'projet_id'    => $request->projet_id,
                'date_effort'  => $request->date_effort,
                'duree_heures' => $request->duree_heures,
                'commentaire'  => $request->commentaire,
            ]);

            if ($request->has('taches')) {
                $feuille->taches()->sync($request->taches);
            } else {
                $feuille->taches()->detach();
            }
        });

        return redirect()->route('feuille_temps.index')->with('success', 'Feuille de temps mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $feuille = FeuilleTemps::findOrFail($id);

        if (Auth::user()->hasRole('Super Admin')) {
            // Accès total
        } elseif (Auth::user()->hasRole('Admin')) {
            if ($feuille->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez supprimer que les feuilles de temps que vous avez créées.');
            }
        } else {
            if ($feuille->created_by !== Auth::id()) {
                abort(403);
            }
        }

        DB::transaction(function () use ($feuille) {
            $feuille->taches()->detach();
            $feuille->delete();
        });

        return redirect()->route('feuille_temps.index')->with('success', 'Feuille de temps supprimée avec succès.');
    }
}

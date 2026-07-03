<?php

namespace App\Http\Controllers;

use App\Models\FeuilleTemps;
use App\Models\Employe;
use App\Models\Projet;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FeuilleTempsController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $query = FeuilleTemps::with(['employe.user', 'projet', 'taches']);
        
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('employe_id', Auth::id());
        }

        $feuilles = $query->get();
        return view('feuille_temps.index', compact('feuilles'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $employes = Auth::user()->hasRole('Admin') ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
        $projets = Projet::all();
        $taches = Tache::all();
        return view('feuille_temps.create', compact('employes', 'projets', 'taches'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'projet_id'    => 'required|exists:projets,id',
            'date_effort'  => 'required|date',
            'duree_heures' => 'required|numeric|min:0.5|max:24',
            'commentaire'  => 'nullable|string',
            'taches'       => 'nullable|array',
            'taches.*'     => 'exists:taches,id',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->employe_id != Auth::id()) {
            abort(403, 'Vous ne pouvez pas créer de feuille de temps pour un autre employé.');
        }

        DB::transaction(function () use ($request) {
            $feuille = FeuilleTemps::create([
                'employe_id'   => $request->employe_id,
                'projet_id'    => $request->projet_id,
                'date_effort'  => $request->date_effort,
                'duree_heures' => $request->duree_heures,
                'commentaire'  => $request->commentaire,
            ]);

            if ($request->has('taches')) {
                $feuille->taches()->attach($request->taches);
            }
        });

        return redirect()->route('feuille_temps.index')->with('success', 'Feuille de temps créée avec succès.');
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

        if (!Auth::user()->hasRole('Admin') && $feuille->employe_id != Auth::id()) {
            abort(403);
        }

        $employes = Auth::user()->hasRole('Admin') ? Employe::with('user')->get() : Employe::where('user_id', Auth::id())->get();
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

        if (!Auth::user()->hasRole('Admin') && $feuille->employe_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'projet_id'    => 'required|exists:projets,id',
            'date_effort'  => 'required|date',
            'duree_heures' => 'required|numeric|min:0.5|max:24',
            'commentaire'  => 'nullable|string',
            'taches'       => 'nullable|array',
            'taches.*'     => 'exists:taches,id',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->employe_id != Auth::id()) {
            abort(403);
        }

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

        if (!Auth::user()->hasRole('Admin') && $feuille->employe_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($feuille) {
            $feuille->taches()->detach();
            $feuille->delete();
        });

        return redirect()->route('feuille_temps.index')->with('success', 'Feuille de temps supprimée avec succès.');
    }
}

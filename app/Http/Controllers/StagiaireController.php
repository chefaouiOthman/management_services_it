<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StagiaireController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:stagiaire-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:stagiaire-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:stagiaire-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:stagiaire-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES STAGIAIRES
     */
    public function index(Request $request)
    {
        $query = Stagiaire::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhere('ecole_origine', 'like', "%{$s}%")
                  ->orWhere('sujet_stage', 'like', "%{$s}%");
            });
        }
        if ($request->filled('ecole_origine')) {
            $query->where('ecole_origine', $request->ecole_origine);
        }
        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }

        $stagiaires = $query->paginate(25)->appends($request->query());
        return view('stagiaires.index', compact('stagiaires'));
    }

    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        $stagiaire = new Stagiaire();
        return view('stagiaires.create', compact('stagiaire'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_complet'   => 'required|string|max:150',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'est_actif'     => 'boolean',
            'ecole_origine' => 'required|string|max:150',
            'sujet_stage'   => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'est_actif'   => $request->input('est_actif', true),
            ]);

            Stagiaire::create([
                'user_id'       => $user->id,
                'ecole_origine' => $request->ecole_origine,
                'sujet_stage'   => $request->sujet_stage,
            ]);

            $user->assignRole('Stagiaire');
        });

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès.');
    }

    /**
     * 4. SHOW : AFFICHER UN STAGIAIRE
     */
    public function show($id)
    {
        $stagiaire = Stagiaire::with('user')->findOrFail($id);
        return view('stagiaires.show', compact('stagiaire'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        $stagiaire = Stagiaire::with('user')->findOrFail($id);
        return view('stagiaires.edit', compact('stagiaire'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        $stagiaire = Stagiaire::findOrFail($id);
        $user = $stagiaire->user;

        $request->validate([
            'nom_complet'   => 'required|string|max:150',
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'      => 'nullable|string|min:8',
            'est_actif'     => 'boolean',
            'ecole_origine' => 'required|string|max:150',
            'sujet_stage'   => 'required|string',
        ]);

        DB::transaction(function () use ($request, $user, $stagiaire) {
            $user->update([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'est_actif'   => $request->has('est_actif') ? $request->est_actif : $user->est_actif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $stagiaire->update([
                'ecole_origine' => $request->ecole_origine,
                'sujet_stage'   => $request->sujet_stage,
            ]);
        });

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $stagiaire = Stagiaire::findOrFail($id);
            if ($stagiaire->user) { $stagiaire->user?->delete(); } // Supprime l'User et cascade sur Stagiaire
        });

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé avec succès.');
    }
}

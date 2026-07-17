<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:departement-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:departement-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:departement-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:departement-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            $departement = auth()->user()->employe?->departement
                ?? auth()->user()->stagiaire?->departement;
            abort_unless($departement, 404, 'Aucun département associé à votre profil.');

            $departement->load(['employes.user', 'stagiaires.user']);
            return view('departements.index', compact('departement'));
        }

        $query = Departement::with(['employes.user', 'stagiaires.user']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom_departement', 'like', "%{$s}%");
            });
        }

        $departements = $query->paginate(25)->appends($request->query());
        return view('departements.index', compact('departements'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('departements.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_departement' => 'required|string|max:100|unique:departements,nom_departement',
        ]);

        DB::transaction(function () use ($request) {
            Departement::create([
                'nom_departement' => $request->nom_departement,
            ]);
        });

        return redirect()->route('departements.index')->with('success', 'Département créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $departement = Departement::findOrFail($id);
        return view('departements.show', compact('departement'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $departement = Departement::findOrFail($id);
        return view('departements.edit', compact('departement'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $departement = Departement::findOrFail($id);

        $request->validate([
            'nom_departement' => ['required', 'string', 'max:100', Rule::unique('departements')->ignore($departement->id)],
        ]);

        DB::transaction(function () use ($request, $departement) {
            $departement->update([
                'nom_departement' => $request->nom_departement,
            ]);
        });

        return redirect()->route('departements.index')->with('success', 'Département mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $departement = Departement::findOrFail($id);

        DB::transaction(function () use ($departement) {
            $departement->delete();
        });

        return redirect()->route('departements.index')->with('success', 'Département supprimé avec succès.');
    }
}

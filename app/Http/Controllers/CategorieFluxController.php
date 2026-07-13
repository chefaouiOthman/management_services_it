<?php

namespace App\Http\Controllers;

use App\Models\CategorieFlux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategorieFluxController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Client')) {
                abort(403, 'Accès interdit.');
            }
            return $next($request);
        }, ['only' => ['index']]);

        $this->middleware('permission:categorie-flux-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categorie-flux-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categorie-flux-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        if (!auth()->user()->hasRole('Admin')) {
            return redirect()->route('flux_tresoreries.index');
        }
        $categories = CategorieFlux::all();
        return view('categorie_flux.index', compact('categories'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        return view('categorie_flux.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        $request->validate([
            'libelle_categorie' => 'required|string|max:100|unique:categorie_flux,libelle_categorie',
            'code_comptable'    => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            CategorieFlux::create([
                'libelle_categorie' => $request->libelle_categorie,
                'code_comptable'    => $request->code_comptable,
            ]);
        });

        return redirect()->back()->with('success', 'Catégorie ajoutée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        $categorie = CategorieFlux::findOrFail($id);
        return view('categorie_flux.show', compact('categorie'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        $categorie = CategorieFlux::findOrFail($id);
        return view('categorie_flux.edit', compact('categorie'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        $categorie = CategorieFlux::findOrFail($id);

        $request->validate([
            'libelle_categorie' => ['required', 'string', 'max:100', Rule::unique('categorie_flux')->ignore($categorie->id)],
            'code_comptable'    => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $categorie) {
            $categorie->update([
                'libelle_categorie' => $request->libelle_categorie,
                'code_comptable'    => $request->code_comptable,
            ]);
        });

        return redirect()->back()->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('Admin')) { abort(403); }
        $categorie = CategorieFlux::findOrFail($id);

        DB::transaction(function () use ($categorie) {
            $categorie->delete();
        });

        return redirect()->back()->with('success', 'Catégorie supprimée avec succès.');
    }
}

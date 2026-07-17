<?php

namespace App\Http\Controllers;

use App\Models\CatalogueFormation;
use App\Models\SupportCours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CatalogueFormationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin|Employe_Standard|Stagiaire|Client', ['only' => ['index', 'show']]);
        $this->middleware('permission:catalogue-formation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:catalogue-formation-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:catalogue-formation-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = CatalogueFormation::with('supportCours');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('titre_formation', 'like', "%{$s}%")
                  ->orWhere('description_programme', 'like', "%{$s}%")
                  ->orWhere('prix_standard', 'like', "%{$s}%");
            });
        }

        $catalogues = $query->paginate(25)->appends($request->query());
        return view('catalogues.index', compact('catalogues'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $supports = SupportCours::all();
        return view('catalogues.create', compact('supports'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre_formation'       => 'required|string|max:200|unique:catalogue_formations,titre_formation',
            'description_programme' => 'required|string',
            'prix_standard'         => 'required|numeric|min:0',
            'supports'              => 'nullable|array',
            'supports.*'            => 'exists:support_cours,id',
        ]);

        DB::transaction(function () use ($request) {
            $catalogue = CatalogueFormation::create([
                'titre_formation'       => $request->titre_formation,
                'description_programme' => $request->description_programme,
                'prix_standard'         => $request->prix_standard,
            ]);

            if ($request->has('supports')) {
                $catalogue->supportCours()->attach($request->supports);
            }
        });

        return redirect()->route('catalogue.index')->with('success', 'Catalogue créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $catalogue = CatalogueFormation::with(['supportCours', 'sessionFormations'])->findOrFail($id);
        return view('catalogues.show', compact('catalogue'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $catalogue = CatalogueFormation::with('supportCours')->findOrFail($id);
        $supports = SupportCours::all();
        return view('catalogues.edit', compact('catalogue', 'supports'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $catalogue = CatalogueFormation::findOrFail($id);

        $request->validate([
            'titre_formation'       => ['required', 'string', 'max:200', Rule::unique('catalogue_formations')->ignore($catalogue->id)],
            'description_programme' => 'required|string',
            'prix_standard'         => 'required|numeric|min:0',
            'supports'              => 'nullable|array',
            'supports.*'            => 'exists:support_cours,id',
        ]);

        DB::transaction(function () use ($request, $catalogue) {
            $catalogue->update([
                'titre_formation'       => $request->titre_formation,
                'description_programme' => $request->description_programme,
                'prix_standard'         => $request->prix_standard,
            ]);

            if ($request->has('supports')) {
                $catalogue->supportCours()->sync($request->supports);
            } else {
                $catalogue->supportCours()->detach();
            }
        });

        return redirect()->route('catalogue.index')->with('success', 'Catalogue mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $catalogue = CatalogueFormation::findOrFail($id);

        DB::transaction(function () use ($catalogue) {
            $catalogue->supportCours()->detach();
            $catalogue->delete();
        });

        return redirect()->route('catalogue.index')->with('success', 'Catalogue supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TypeMateriel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TypeMaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Client')) {
                abort(403, 'Accès interdit.');
            }
            return $next($request);
        }, ['only' => ['index', 'show']]);

        $this->middleware('permission:type-materiel-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:type-materiel-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:type-materiel-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $types = TypeMateriel::all();
        return view('type_materiels.index', compact('types'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $this->denyInventaireMutation();

        return view('type_materiels.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $this->denyInventaireMutation();

        $request->validate([
            'libelle_type'     => 'required|string|max:100|unique:type_materiels,libelle_type',
            'description_type' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            TypeMateriel::create([
                'libelle_type'     => $request->libelle_type,
                'description_type' => $request->description_type,
            ]);
        });

        return redirect()->back()->with('success', 'Type de matériel créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $type = TypeMateriel::with('assetMateriels')->findOrFail($id);
        return view('type_materiels.show', compact('type'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $this->denyInventaireMutation();

        $type = TypeMateriel::findOrFail($id);
        return view('type_materiels.edit', compact('type'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $this->denyInventaireMutation();

        $type = TypeMateriel::findOrFail($id);

        $request->validate([
            'libelle_type'     => ['required', 'string', 'max:100', Rule::unique('type_materiels')->ignore($type->id)],
            'description_type' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $type) {
            $type->update([
                'libelle_type'     => $request->libelle_type,
                'description_type' => $request->description_type,
            ]);
        });

        return redirect()->back()->with('success', 'Type de matériel mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $this->denyInventaireMutation();

        $type = TypeMateriel::findOrFail($id);

        DB::transaction(function () use ($type) {
            $type->delete();
        });

        return redirect()->back()->with('success', 'Type de matériel supprimé avec succès.');
    }
}

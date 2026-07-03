<?php

namespace App\Http\Controllers;

use App\Models\AssetMateriel;
use App\Models\TypeMateriel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetMaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:asset-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:asset-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:asset-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:asset-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $assets = AssetMateriel::with('typeMateriel')->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $types = TypeMateriel::all();
        return view('assets.create', compact('types'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'type_materiel_id' => 'required|exists:type_materiels,id',
            'num_serie'        => 'required|string|max:100|unique:asset_materiels,num_serie',
            'marque'           => 'required|string|max:100',
            'modele'           => 'required|string|max:100',
            'date_achat_actif' => 'nullable|date',
            'statut_materiel'  => 'required|in:disponible,attribue,en_panne,reforme',
            'prix_achat'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            AssetMateriel::create([
                'type_materiel_id' => $request->type_materiel_id,
                'num_serie'        => $request->num_serie,
                'marque'           => $request->marque,
                'modele'           => $request->modele,
                'date_achat_actif' => $request->date_achat_actif,
                'statut_materiel'  => $request->statut_materiel,
                'prix_achat'       => $request->prix_achat,
            ]);
        });

        return redirect()->route('assets.index')->with('success', 'Matériel ajouté avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $asset = AssetMateriel::with(['typeMateriel', 'ticketsMaintenance'])->findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $asset = AssetMateriel::findOrFail($id);
        $types = TypeMateriel::all();
        return view('assets.edit', compact('asset', 'types'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $asset = AssetMateriel::findOrFail($id);

        $request->validate([
            'type_materiel_id' => 'required|exists:type_materiels,id',
            'num_serie'        => ['required', 'string', 'max:100', Rule::unique('asset_materiels')->ignore($asset->id)],
            'marque'           => 'required|string|max:100',
            'modele'           => 'required|string|max:100',
            'date_achat_actif' => 'nullable|date',
            'statut_materiel'  => 'required|in:disponible,attribue,en_panne,reforme',
            'prix_achat'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $asset) {
            $asset->update([
                'type_materiel_id' => $request->type_materiel_id,
                'num_serie'        => $request->num_serie,
                'marque'           => $request->marque,
                'modele'           => $request->modele,
                'date_achat_actif' => $request->date_achat_actif,
                'statut_materiel'  => $request->statut_materiel,
                'prix_achat'       => $request->prix_achat,
            ]);
        });

        return redirect()->route('assets.index')->with('success', 'Matériel mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $asset = AssetMateriel::findOrFail($id);

        DB::transaction(function () use ($asset) {
            $asset->delete();
        });

        return redirect()->route('assets.index')->with('success', 'Matériel supprimé avec succès.');
    }
}

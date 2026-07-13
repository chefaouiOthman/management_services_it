<?php

namespace App\Http\Controllers;

use App\Models\AssetMateriel;
use App\Models\TicketMaintenance;
use App\Models\TypeMateriel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetMaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Client')) {
                abort(403, 'Accès interdit.');
            }
            return $next($request);
        }, ['only' => ['index', 'show']]);

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
        $this->denyInventaireMutation();

        $types = TypeMateriel::all();
        $asset = new \App\Models\AssetMateriel();
        return view('assets.create', compact('types', 'asset'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $this->denyInventaireMutation();

        $request->validate([
            'type_materiel_id'  => 'required',
            'new_type_materiel' => 'required_if:type_materiel_id,autre|string|max:100',
            'num_serie'         => 'required|string|max:100|unique:asset_materiels,num_serie',
            'marque'            => 'required|string|max:100',
            'modele'            => 'required|string|max:100',
            'date_achat_actif'  => 'nullable|date',
            'statut_materiel'   => 'required|in:disponible,attribue,en_panne,reforme',
            'prix_achat'        => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $typeId = $request->type_materiel_id;
            
            if ($typeId === 'autre') {
                $newType = TypeMateriel::create([
                    'libelle_type' => $request->new_type_materiel,
                ]);
                $typeId = $newType->id;
            } else {
                // S'assurer qu'il existe
                $request->validate(['type_materiel_id' => 'exists:type_materiels,id']);
            }

            AssetMateriel::create([
                'type_materiel_id' => $typeId,
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
        $asset = AssetMateriel::with('typeMateriel')->findOrFail($id);
        $tickets = TicketMaintenance::where('asset_materiel_id', $id)->with('user')->latest()->get();
        return view('assets.show', compact('asset', 'tickets'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $this->denyInventaireMutation();

        $asset = AssetMateriel::findOrFail($id);
        $types = TypeMateriel::all();
        return view('assets.edit', compact('asset', 'types'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $this->denyInventaireMutation();

        $asset = AssetMateriel::findOrFail($id);

        $request->validate([
            'type_materiel_id'  => 'required',
            'new_type_materiel' => 'required_if:type_materiel_id,autre|string|max:100',
            'num_serie'         => ['required', 'string', 'max:100', Rule::unique('asset_materiels')->ignore($asset->id)],
            'marque'            => 'required|string|max:100',
            'modele'            => 'required|string|max:100',
            'date_achat_actif'  => 'nullable|date',
            'statut_materiel'   => 'required|in:disponible,attribue,en_panne,reforme',
            'prix_achat'        => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $asset) {
            $typeId = $request->type_materiel_id;
            
            if ($typeId === 'autre') {
                $newType = TypeMateriel::create([
                    'libelle_type' => $request->new_type_materiel,
                ]);
                $typeId = $newType->id;
            } else {
                $request->validate(['type_materiel_id' => 'exists:type_materiels,id']);
            }

            $asset->update([
                'type_materiel_id' => $typeId,
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
        $this->denyInventaireMutation();

        $asset = AssetMateriel::findOrFail($id);

        DB::transaction(function () use ($asset) {
            $asset->delete();
        });

        return redirect()->route('assets.index')->with('success', 'Matériel supprimé avec succès.');
    }
}

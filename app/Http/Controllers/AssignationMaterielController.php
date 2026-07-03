<?php

namespace App\Http\Controllers;

use App\Models\AssignationMateriel;
use App\Models\AssetMateriel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignationMaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:assignation-materiel-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:assignation-materiel-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:assignation-materiel-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:assignation-materiel-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $assignations = AssignationMateriel::with(['user', 'assetMateriel'])->get();
        return view('assignation_materiels.index', compact('assignations'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $users = User::all();
        // On ne propose que les matériels disponibles
        $assets = AssetMateriel::where('statut_materiel', 'disponible')->get();
        return view('assignation_materiels.create', compact('users', 'assets'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'           => 'required|exists:users,id',
            'asset_materiel_id' => 'required|exists:asset_materiels,id',
            'date_remise'       => 'required|date',
            'date_restitution'  => 'nullable|date|after_or_equal:date_remise',
        ]);

        DB::transaction(function () use ($request) {
            AssignationMateriel::create([
                'user_id'           => $request->user_id,
                'asset_materiel_id' => $request->asset_materiel_id,
                'date_remise'       => $request->date_remise,
                'date_restitution'  => $request->date_restitution,
            ]);

            // Mettre à jour le statut de l'actif
            AssetMateriel::where('id', $request->asset_materiel_id)->update(['statut_materiel' => 'attribue']);
        });

        return redirect()->route('assignation_materiels.index')->with('success', 'Assignation créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $assignation = AssignationMateriel::with(['user', 'assetMateriel'])->findOrFail($id);
        return view('assignation_materiels.show', compact('assignation'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $assignation = AssignationMateriel::findOrFail($id);
        $users = User::all();
        // On inclut le matériel actuellement assigné dans la liste
        $assets = AssetMateriel::where('statut_materiel', 'disponible')
                               ->orWhere('id', $assignation->asset_materiel_id)
                               ->get();

        return view('assignation_materiels.edit', compact('assignation', 'users', 'assets'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $assignation = AssignationMateriel::findOrFail($id);

        $request->validate([
            'user_id'           => 'required|exists:users,id',
            'asset_materiel_id' => 'required|exists:asset_materiels,id',
            'date_remise'       => 'required|date',
            'date_restitution'  => 'nullable|date|after_or_equal:date_remise',
        ]);

        DB::transaction(function () use ($request, $assignation) {
            $oldAssetId = $assignation->asset_materiel_id;
            
            $assignation->update([
                'user_id'           => $request->user_id,
                'asset_materiel_id' => $request->asset_materiel_id,
                'date_remise'       => $request->date_remise,
                'date_restitution'  => $request->date_restitution,
            ]);

            // Gestion du changement de matériel
            if ($oldAssetId != $request->asset_materiel_id) {
                AssetMateriel::where('id', $oldAssetId)->update(['statut_materiel' => 'disponible']);
                AssetMateriel::where('id', $request->asset_materiel_id)->update(['statut_materiel' => 'attribue']);
            }

            // Si la date de restitution est remplie, le matériel redevient disponible
            if ($request->filled('date_restitution')) {
                AssetMateriel::where('id', $request->asset_materiel_id)->update(['statut_materiel' => 'disponible']);
            }
        });

        return redirect()->route('assignation_materiels.index')->with('success', 'Assignation mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $assignation = AssignationMateriel::findOrFail($id);

        DB::transaction(function () use ($assignation) {
            // Libérer le matériel si l'assignation n'était pas terminée
            if (is_null($assignation->date_restitution)) {
                AssetMateriel::where('id', $assignation->asset_materiel_id)->update(['statut_materiel' => 'disponible']);
            }
            $assignation->delete();
        });

        return redirect()->route('assignation_materiels.index')->with('success', 'Assignation supprimée avec succès.');
    }
}

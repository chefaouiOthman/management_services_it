<?php

namespace App\Http\Controllers;

use App\Models\AssetMateriel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignationMaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-assets');
    }

    /**
     * Assigner un matériel à un utilisateur
     */
    public function store(Request $request, AssetMateriel $asset)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_remise' => 'required|date',
        ]);

        if ($asset->statut_materiel !== 'disponible') {
            return back()->with('error', 'Ce matériel n\'est pas disponible pour une assignation.');
        }

        DB::transaction(function () use ($request, $asset) {
            // 1. Assigner via le pivot
            $asset->users()->attach($request->user_id, [
                'date_remise' => $request->date_remise,
            ]);

            // 2. Mettre à jour le statut du matériel
            $asset->update(['statut_materiel' => 'attribue']);
        });

        return redirect()->route('asset_materiels.show', $asset->id)->with('success', 'Matériel assigné avec succès.');
    }

    /**
     * Restituer un matériel (via Alpine/Fetch asynchrone)
     */
    public function restituer(Request $request, $id)
    {
        // $id est l'ID de la table pivot (assignation_materiels.id)
        
        try {
            DB::transaction(function () use ($id) {
                // Trouver la ligne pivot correspondante
                $assignation = DB::table('assignation_materiels')->where('id', $id)->first();
                
                if (!$assignation) {
                    throw new \Exception("Assignation introuvable.");
                }

                if ($assignation->date_restitution !== null) {
                    throw new \Exception("Ce matériel a déjà été restitué.");
                }

                // 1. Mettre à jour la date de restitution
                DB::table('assignation_materiels')->where('id', $id)->update([
                    'date_restitution' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // 2. Mettre à jour le statut du matériel à 'disponible'
                $asset = AssetMateriel::find($assignation->asset_materiel_id);
                // Si l'actif était en panne, on ne le remet pas forcément disponible, mais selon la logique métier :
                // La restitution libère l'actif. S'il était en panne, il reste en panne ?
                // Si un collaborateur le rend car il est cassé, il sera "en_panne".
                // Le mieux est de le remettre 'disponible' SAUF s'il est actuellement 'en_panne'.
                if ($asset->statut_materiel !== 'en_panne') {
                    $asset->update(['statut_materiel' => 'disponible']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Matériel restitué avec succès.',
                'date_restitution' => Carbon::now()->format('d/m/Y')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AssetMateriel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignationMaterielController extends Controller
{
    /**
     * Auth guard: Admin or any user with the manage-assets permission.
     * We intentionally do NOT use the Spatie middleware in __construct() to
     * avoid a crash when the permission record doesn't exist in the DB.
     * Instead we perform a manual check inside each action.
     */
    private function authorizeAction(): void
    {
        $user = Auth::user();
        $allowed = $user->hasRole('Admin')
            || $user->hasRole('Manager')
            || (method_exists($user, 'hasPermissionTo') && $user->can('manage-assets'));

        if (! $allowed) {
            abort(403, 'Action non autorisée.');
        }
    }

    /**
     * Assigner un matériel à un utilisateur (POST assets/{asset}/assigner)
     */
    public function store(Request $request, AssetMateriel $asset)
    {
        // 1. Sécurité maximale pour récupérer l'ID du matériel
        // On cherche dans l'URL ($assetId), ou dans le formulaire via 'asset_materiel_id' ou 'asset_id'
        $finalAssetId = $assetId 
            ?? $request->input('asset_materiel_id') 
            ?? $request->input('asset_id');

        // Si vraiment introuvable, on renvoie une erreur propre sans crasher
        if (!$finalAssetId) {
            return back()->withErrors(['error' => 'L\'identifiant du matériel est introuvable.'])->withInput();
        }

        // 2. Validation des autres données
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_remise' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Clôturer l'ancienne assignation active s'il y en a une
            DB::table('assignation_materiels')
                ->where('asset_materiel_id', $finalAssetId)
                ->whereNull('date_restitution')
                ->update(['date_restitution' => $request->date_remise]);

            // Insérer la nouvelle assignation avec l'ID garanti non-null
            DB::table('assignation_materiels')->insert([
                'user_id' => $request->user_id,
                'asset_materiel_id' => $finalAssetId, // <- Garanti non-null maintenant !
                'date_remise' => $request->date_remise,
                'date_restitution' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mettre à jour le statut du matériel en 'attribue'
            // (Vérifie si ta table s'appelle 'assets' ou 'asset_materiels')
            DB::table('assets') // ou 'asset_materiels' selon ta structure
                ->where('id', $finalAssetId)
                ->update(['statut_materiel' => 'attribue']);

            DB::commit();

            return back()->with('success', 'Le matériel a bien été assigné au collaborateur.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'assignation : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Restituer un matériel (PATCH assignations/{id}/restituer — async Alpine/Fetch)
     */
    public function restituer(Request $request, $id)
    {
        $this->authorizeAction();

        try {
            DB::transaction(function () use ($id) {
                $assignation = DB::table('assignation_materiels')->where('id', $id)->first();

                if (! $assignation) {
                    throw new \Exception('Assignation introuvable.');
                }

                if ($assignation->date_restitution !== null) {
                    throw new \Exception('Ce matériel a déjà été restitué.');
                }

                DB::table('assignation_materiels')->where('id', $id)->update([
                    'date_restitution' => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ]);

                $asset = AssetMateriel::find($assignation->asset_materiel_id);
                if ($asset && $asset->statut_materiel !== 'en_panne') {
                    $asset->update(['statut_materiel' => 'disponible']);
                }
            });

            return response()->json([
                'success'          => true,
                'message'          => 'Matériel restitué avec succès.',
                'date_restitution' => Carbon::now()->format('d/m/Y'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Supprimer une assignation (DELETE assignation_materiels/{id})
     */
    public function destroy($id)
    {
        $this->authorizeAction();

        DB::transaction(function () use ($id) {
            $assignation = DB::table('assignation_materiels')->where('id', $id)->first();
            if (! $assignation) {
                return;
            }

            DB::table('assignation_materiels')->where('id', $id)->delete();

            // Revert asset status to disponible if no other active assignment remains
            $hasOtherActive = DB::table('assignation_materiels')
                ->where('asset_materiel_id', $assignation->asset_materiel_id)
                ->whereNull('date_restitution')
                ->exists();

            if (! $hasOtherActive) {
                $asset = AssetMateriel::find($assignation->asset_materiel_id);
                if ($asset && $asset->statut_materiel === 'attribue') {
                    $asset->update(['statut_materiel' => 'disponible']);
                }
            }
        });

        return back()->with('success', 'Assignation supprimée avec succès.');
    }
}

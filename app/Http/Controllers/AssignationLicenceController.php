<?php

namespace App\Http\Controllers;

use App\Models\LicenceLogiciel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class AssignationLicenceController extends Controller
{
    use FilterSuperAdmin;
    public function __construct()
    {
        $this->middleware('permission:manage-assets');
    }

    /**
     * Assigner une licence à un utilisateur
     */
    public function store(Request $request, $licence_id)
    {
        $this->denyInventaireMutation();

        $licence = LicenceLogiciel::findOrFail($licence_id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_attribution' => 'required|date',
        ]);

        $this->validateNotSuperAdminTarget($request);

        DB::transaction(function () use ($request, $licence) {
            $licence->users()->attach($request->user_id, [
                'date_attribution' => $request->date_attribution,
            ]);
        });

        return redirect()->route('licences.index')->with('success', 'Licence assignée avec succès.');
    }

    /**
     * Révoquer une licence (via Alpine/Fetch asynchrone)
     */
    public function revoquer(Request $request, $id)
    {
        $this->denyInventaireMutation();

        try {
            DB::transaction(function () use ($id) {
                $assignation = DB::table('assignation_licences')->where('id', $id)->first();
                
                if (!$assignation) {
                    throw new \Exception("Assignation introuvable.");
                }

                if ($assignation->date_revocation !== null) {
                    throw new \Exception("Cette licence a déjà été révoquée.");
                }

                DB::table('assignation_licences')->where('id', $id)->update([
                    'date_revocation' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Licence révoquée avec succès.',
                'date_revocation' => Carbon::now()->format('d/m/Y')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

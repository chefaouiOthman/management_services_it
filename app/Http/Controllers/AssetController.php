<?php

namespace App\Http\Controllers;

use App\Models\AssetMateriel;
use App\Models\TypeMateriel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * LISTE DE L'INVENTAIRE
     */
    public function index()
    {
        $assets = AssetMateriel::with(['typeMateriel', 'users'])->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * FORMULAIRE D'AJOUT D'UN MATÉRIEL
     */
    public function create()
    {
        $types = TypeMateriel::all();
        $users = User::all(); // Pour l'assignation optionnelle au moment de la création
        return view('assets.create', compact('types', 'users'));
    }

    /**
     * ENREGISTREMENT ET ASSIGNATION OPTIONNELLE
     */
    public function store(Request $request)
    {
        $request->validate([
            'num_serie'        => 'required|string|max:100|unique:asset_materiels',
            'marque'           => 'required|string|max:100',
            'modele'           => 'required|string|max:100',
            'statut'           => 'required|in:disponible,attribue,en_panne,reforme',
            'type_materiel_id' => 'required|exists:type_materiels,id',
            'user_id'          => 'nullable|exists:users,id', // L'attribution n'est pas obligatoire dès le départ (0,1)
        ]);

        DB::transaction(function () use ($request) {
            // 1. Création de l'asset
            $asset = AssetMateriel::create([
                'num_serie'        => $request->num_serie,
                'marque'           => $request->marque,
                'modele'           => $request->modele,
                'statut'           => $request->statut,
                'type_materiel_id' => $request->type_materiel_id,
            ]);

            // 2. Si un utilisateur est sélectionné, on crée l'assignation dans le pivot
            if ($request->filled('user_id')) {
                $asset->users()->attach($request->user_id, ['date_assignation' => now()]);
            }
        });

        return redirect()->route('assets.index')->with('success', 'Matériel enregistré dans le parc.');
    }
}

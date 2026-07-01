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
        // On récupère les matériels avec leurs relations (Eager Loading)
        $assets = AssetMateriel::with(['typeMateriel', 'users'])->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * FORMULAIRE D'AJOUT D'UN MATÉRIEL
     */
    public function create()
    {
        // On récupère les données nécessaires pour alimenter les listes déroulantes du formulaire
        $types = TypeMateriel::all();
        $users = User::all();
        return view('assets.create', compact('types', 'users'));
    }

    /**
     * ENREGISTREMENT ET ASSIGNATION OPTIONNELLE
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire (Adaptée aux noms du modèle)
        $request->validate([
            'num_serie'        => 'required|string|max:100|unique:asset_materiels,num_serie',
            'marque'           => 'required|string|max:100',
            'modele'           => 'required|string|max:100',
            'statut_materiel'  => 'required|in:disponible,attribue,en_panne,reforme', // Modifié ici !
            'type_materiel_id' => 'required|exists:type_materiels,id',
            'user_id'          => 'nullable|exists:users,id',
        ]);

        // Utilisation de la transaction pour garantir le "Tout ou Rien"
        DB::transaction(function () use ($request) {

            // 1. Création de l'asset en utilisant les clés exactes du modèle
            $asset = AssetMateriel::create([
                'num_serie'        => $request->num_serie,
                'marque'           => $request->marque,
                'modele'           => $request->modele,
                'statut_materiel'  => $request->statut_materiel, // Modifié ici !
                'type_materiel_id' => $request->type_materiel_id,
            ]);

            // 2. Si un utilisateur est sélectionné, on écrit dans la table pivot via la relation ajoutée
            if ($request->filled('user_id')) {
                $asset->users()->attach($request->user_id, ['date_assignation' => now()]);
            }
        });

        // Redirection vers la liste avec un message Flash de succès
        return redirect()->route('assets.index')->with('success', 'Matériel enregistré dans le parc.');
    }
}

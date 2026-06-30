<?php

namespace App\Http\Controllers;

use App\Models\CatalogueFormation;
use App\Models\SupportCours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogueFormationController extends Controller
{
    public function index()
    {
        $formations = CatalogueFormation::with('supports')->get();
        return view('formations.catalogue.index', compact('formations'));
    }

    public function create()
    {
        return view('formations.catalogue.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'         => 'required|string|max:150',
            'description'   => 'required|string',
            'duree_heures'  => 'required|integer|min:1',
            'nom_support'   => 'nullable|string|max:150',
            'url_document'  => 'nullable|url',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Création du cours dans le catalogue
            $formation = CatalogueFormation::create([
                'titre'        => $request->titre,
                'description'  => $request->description,
                'duree_heures' => $request->duree_heures,
            ]);

            // 2. Ajout du support de cours si renseigné (Relation 1-N dépendante)
            if ($request->filled('nom_support')) {
                SupportCours::create([
                    'catalogue_formation_id' => $formation->id,
                    'nom_support'            => $request->nom_support,
                    'url_document'           => $request->url_document,
                ]);
            }
        });

        return redirect()->route('catalogue.index')->with('success', 'Formation ajoutée au catalogue.');
    }
}

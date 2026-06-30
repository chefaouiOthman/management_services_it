<?php

namespace App\Http\Controllers;

use App\Models\FeuilleTemps;
use App\Models\Tache;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeuilleTempsController extends Controller
{
    /**
     * LISTE DES FEUILLES DE TEMPS
     */
    public function index()
    {
        $feuilles = FeuilleTemps::with(['employe.user', 'taches'])->get();
        return view('feuille_temps.index', compact('feuilles'));
    }

    /**
     * FORMULAIRE DE CREATION
     */
    public function create()
    {
        $taches = Tache::all(); // Pour associer des tâches à la feuille de temps
        return view('feuille_temps.create', compact('taches'));
    }

    /**
     * ENREGISTREMENT ET VENTILATION DE LA FEUILLE DE TEMPS
     */
    public function store(Request $request)
    {
        $request->validate([
            'date_soumission' => 'required|date',
            'heures_declarees'=> 'required|numeric|min:0.5',
            'tache_id'        => 'required|exists:taches,id', // Tâche principale liée
        ]);

        // L'ID de l'employé correspond à l'ID de l'User connecté (Héritage 1-1)
        $employeId = auth()->id();

        DB::transaction(function () use ($request, $employeId) {
            // 1. Création de la feuille de temps
            $feuille = FeuilleTemps::create([
                'employe_id'       => $employeId,
                'date_soumission'  => $request->date_soumission,
                'heures_declarees' => $request->heures_declarees,
                'statut_validation'=> 'en_attente', // Statut par défaut
            ]);

            // 2. Liaison immédiate dans la table pivot feuille_temps_tache
            $feuille->taches()->attach($request->tache_id);
        });

        return redirect()->route('feuille_temps.index')->with('success', 'Feuille de temps soumise pour validation.');
    }
}

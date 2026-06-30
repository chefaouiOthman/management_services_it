<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\Employe;
use Illuminate\Http\Request;

class PointageController extends Controller
{
    /**
     * LISTE DES POINTAGES
     */
    public function index()
    {
        // Récupère les pointages avec l'identité de l'employé
        $pointages = Pointage::with('employe.user')->get();
        return view('pointages.index', compact('pointages'));
    }

    /**
     * ENREGISTRER UN POINTAGE (ENTRÉE OU SORTIE)
     */
    public function store(Request $request)
    {
        // Si l'utilisateur est un employé standard, il pointe pour lui-même.
        // Si c'est l'admin, il peut pointer pour n'importe quel employe_id passé dans la requête.
        $employeId = auth()->user()->hasRole('Admin') ? $request->employe_id : auth()->id();

        $request->validate([
            'type_pointage' => 'required|in:entree,sortie',
            'horodatage'    => 'required|date',
        ]);

        Pointage::create([
            'employe_id'    => $employeId,
            'type_pointage' => $request->type_pointage,
            'horodatage'    => $request->horodatage,
        ]);

        return redirect()->back()->with('success', 'Pointage enregistré avec succès.');
    }
}

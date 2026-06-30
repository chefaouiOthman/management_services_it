<?php

namespace App\Http\Controllers;

use App\Models\SessionFormation;
use App\Models\CatalogueFormation;
use App\Models\Employe;
use Illuminate\Http\Request;

class SessionFormationController extends Controller
{
    public function index()
    {
        $sessions = SessionFormation::with(['catalogueFormation', 'formateur.user'])->get();
        return view('formations.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $formations = CatalogueFormation::all();
        $formateurs = Employe::with('user')->get(); // Tous les employés peuvent être formateurs
        return view('formations.sessions.create', compact('formations', 'formateurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
            'employe_id'              => 'required|exists:employes,id', // Le formateur référent
            'date_debut'              => 'required|date|after_or_equal:today',
            'date_fin'                => 'required|date|after_or_equal:date_debut',
            'emplacement'             => 'required|string|max:150',
        ]);

        SessionFormation::create($request->all());

        return redirect()->route('sessions.index')->with('success', 'Session de formation planifiée.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LicenceLogiciel;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicenceController extends Controller
{
    public function index()
    {
        $licences = LicenceLogiciel::with('employes.user')->get();
        return view('licences.index', compact('licences'));
    }

    public function create()
    {
        $employes = Employe::with('user')->get();
        return view('licences.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_logiciel' => 'required|string|max:150',
            'cle_licence'  => 'required|string|max:255|unique:licence_logiciels',
            'date_expiration' => 'nullable|date|after:today',
            'employe_id'   => 'nullable|exists:employes,id',
        ]);

        DB::transaction(function () use ($request) {
            $licence = LicenceLogiciel::create([
                'nom_logiciel'    => $request->nom_logiciel,
                'cle_licence'     => $request->cle_licence,
                'date_expiration' => $request->date_expiration,
            ]);

            if ($request->filled('employe_id')) {
                $licence->employes()->attach($request->employe_id, ['date_attribution' => now()]);
            }
        });

        return redirect()->route('licences.index')->with('success', 'Licence logicielle ajoutée.');
    }
}

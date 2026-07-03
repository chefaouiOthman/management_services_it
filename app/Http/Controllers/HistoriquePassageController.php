<?php

namespace App\Http\Controllers;

use App\Models\HistoriquePassage;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoriquePassageController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $historiques = HistoriquePassage::with(['user', 'zone'])->get();
        return view('historiques.index', compact('historiques'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $users = User::all();
        $zones = Zone::all();
        return view('historiques.create', compact('users', 'zones'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'zone_id'          => 'required|exists:zones,id',
            'horodatage'       => 'required|date_format:Y-m-d H:i:s',
            'tentative_statut' => 'required|in:autorise,refuse_niveau_insuffisant,refuse_zone_desactivee',
        ]);

        DB::transaction(function () use ($request) {
            HistoriquePassage::create([
                'user_id'          => $request->user_id,
                'zone_id'          => $request->zone_id,
                'horodatage'       => $request->horodatage,
                'tentative_statut' => $request->tentative_statut,
            ]);
        });

        return redirect()->route('historiques.index')->with('success', 'Historique enregistré avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $historique = HistoriquePassage::with(['user', 'zone'])->findOrFail($id);
        return view('historiques.show', compact('historique'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $historique = HistoriquePassage::findOrFail($id);
        $users = User::all();
        $zones = Zone::all();
        return view('historiques.edit', compact('historique', 'users', 'zones'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $historique = HistoriquePassage::findOrFail($id);

        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'zone_id'          => 'required|exists:zones,id',
            'horodatage'       => 'required|date_format:Y-m-d H:i:s',
            'tentative_statut' => 'required|in:autorise,refuse_niveau_insuffisant,refuse_zone_desactivee',
        ]);

        DB::transaction(function () use ($request, $historique) {
            $historique->update([
                'user_id'          => $request->user_id,
                'zone_id'          => $request->zone_id,
                'horodatage'       => $request->horodatage,
                'tentative_statut' => $request->tentative_statut,
            ]);
        });

        return redirect()->route('historiques.index')->with('success', 'Historique mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $historique = HistoriquePassage::findOrFail($id);

        DB::transaction(function () use ($historique) {
            $historique->delete();
        });

        return redirect()->route('historiques.index')->with('success', 'Historique supprimé avec succès.');
    }
}

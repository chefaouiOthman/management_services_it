<?php

namespace App\Http\Controllers;

use App\Models\SupportCours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportCoursController extends Controller
{
    /**
     * 1. INDEX
     */
    public function index()
    {
        $supports = SupportCours::with('catalogueFormations')->get();
        return view('supports.index', compact('supports'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('supports.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_fichier'  => 'required|string|max:150',
            'url_stockage' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            SupportCours::create([
                'nom_fichier'  => $request->nom_fichier,
                'url_stockage' => $request->url_stockage,
            ]);
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours ajouté avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $support = SupportCours::with('catalogueFormations')->findOrFail($id);
        return view('supports.show', compact('support'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $support = SupportCours::findOrFail($id);
        return view('supports.edit', compact('support'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $support = SupportCours::findOrFail($id);

        $request->validate([
            'nom_fichier'  => 'required|string|max:150',
            'url_stockage' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $support) {
            $support->update([
                'nom_fichier'  => $request->nom_fichier,
                'url_stockage' => $request->url_stockage,
            ]);
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $support = SupportCours::findOrFail($id);

        DB::transaction(function () use ($support) {
            $support->catalogueFormations()->detach();
            $support->delete();
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours supprimé avec succès.');
    }
}

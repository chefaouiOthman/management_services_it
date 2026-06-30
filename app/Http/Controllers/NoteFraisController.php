<?php

namespace App\Http\Controllers;

use App\Models\NoteDeFrais;
use Illuminate\Http\Request;

class NoteFraisController extends Controller
{
    public function index()
    {
        // L'admin voit tout le monde, l'employé standard ne verra que les siennes
        $notes = auth()->user()->hasRole('Admin')
            ? NoteDeFrais::with('employe.user')->get()
            : NoteDeFrais::where('employe_id', auth()->id())->get();

        return view('finance.notes.index', compact('notes'));
    }

    public function create()
    {
        return view('finance.notes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description'  => 'required|string|max:255',
            'montant'      => 'required|numeric|min:0.01',
            'date_depense' => 'required|date|before_or_equal:today',
        ]);

        NoteDeFrais::create([
            'employe_id'   => auth()->id(), // Héritage ID relié à l'User connecté
            'description'  => $request->description,
            'montant'      => $request->montant,
            'date_depense' => $request->date_depense,
            'statut'       => 'en_attente',
        ]);

        return redirect()->route('notes.index')->with('success', 'Note de frais soumise.');
    }

    /**
     * APPROBATION / REJET PAR L'ADMIN
     */
    public function updateStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:approuve,rejete'
        ]);

        $note = NoteDeFrais::findOrFail($id);
        $note->update(['statut' => $request->statut]);

        return redirect()->back()->with('success', 'Statut de la note de frais mis à jour.');
    }
}

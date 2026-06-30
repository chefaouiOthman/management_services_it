<?php

namespace App\Http\Controllers;

use App\Models\TicketMaintenance;
use App\Models\AssetMateriel;
use Illuminate\Http\Request;

class TicketMaintenanceController extends Controller
{
    public function index()
    {
        // L'admin voit tout le parc, un utilisateur classique ne verra que ses tickets si besoin
        $tickets = TicketMaintenance::with(['assetMateriel', 'employe.user'])->get();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $assets = AssetMateriel::all();
        return view('tickets.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_materiel_id' => 'required|exists:asset_materiels,id',
            'description_panne' => 'required|string',
            'priorite'          => 'required|in:basse,moyenne,haute,critique',
        ]);

        TicketMaintenance::create([
            'asset_materiel_id' => $request->asset_materiel_id,
            'employe_id'        => auth()->id(), // L'employé connecté qui signale le problème
            'description_panne' => $request->description_panne,
            'priorite'          => $request->priorite,
            'statut_ticket'     => 'ouvert', // Statut initial par défaut
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket de maintenance ouvert.');
    }
}

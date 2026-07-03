<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PointageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pointage-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:pointage-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pointage-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pointage-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES POINTAGES
     */
    public function index()
    {
        // Si l'utilisateur est admin, il voit tout. Sinon, il ne voit que les siens.
        $query = Pointage::with('user');
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('user_id', Auth::id());
        }
        
        $pointages = $query->get();
        return view('pointages.index', compact('pointages'));
    }

    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        // Seul l'admin peut créer un pointage pour quelqu'un d'autre manuellement.
        $users = Auth::user()->hasRole('Admin') ? User::all() : collect([Auth::user()]);
        return view('pointages.create', compact('users'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date_format:Y-m-d H:i:s',
            'heure_depart'    => 'nullable|date_format:Y-m-d H:i:s|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        // Vérification de sécurité : un non-admin ne peut pointer que pour lui-même
        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403, 'Vous ne pouvez pas pointer pour un autre utilisateur.');
        }

        DB::transaction(function () use ($request) {
            Pointage::create([
                'user_id'         => $request->user_id,
                'date_jour'       => $request->date_jour,
                'heure_arrivee'   => $request->heure_arrivee,
                'heure_depart'    => $request->heure_depart,
                'statut_presence' => $request->statut_presence,
            ]);
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage enregistré avec succès.');
    }

    /**
     * 4. SHOW : AFFICHER UN POINTAGE
     */
    public function show($id)
    {
        $pointage = Pointage::with('user')->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $pointage->user_id != Auth::id()) {
            abort(403);
        }

        return view('pointages.show', compact('pointage'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        $pointage = Pointage::with('user')->findOrFail($id);
        
        if (!Auth::user()->hasRole('Admin') && $pointage->user_id != Auth::id()) {
            abort(403);
        }

        $users = Auth::user()->hasRole('Admin') ? User::all() : collect([Auth::user()]);
        return view('pointages.edit', compact('pointage', 'users'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        $pointage = Pointage::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $pointage->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date_format:Y-m-d H:i:s',
            'heure_depart'    => 'nullable|date_format:Y-m-d H:i:s|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        DB::transaction(function () use ($request, $pointage) {
            $pointage->update([
                'date_jour'       => $request->date_jour,
                'heure_arrivee'   => $request->heure_arrivee,
                'heure_depart'    => $request->heure_depart,
                'statut_presence' => $request->statut_presence,
            ]);
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        $pointage = Pointage::findOrFail($id);

        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Seul un administrateur peut supprimer un pointage.');
        }

        DB::transaction(function () use ($pointage) {
            $pointage->delete();
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage supprimé avec succès.');
    }
}

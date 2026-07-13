<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        $query = Pointage::with('user')->orderByDesc('date_jour');
        
        if (Auth::user()->hasRole('Admin')) {
            // Admin : peut filtrer par employé via query string ?user_id=
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            $users = User::orderBy('nom_complet')->get(['id', 'nom_complet']);
        } else {
            // Non-admin : uniquement ses propres pointages
            $query->where('user_id', Auth::id());
            $users = collect();
        }
        
        $pointages = $query->paginate(50);
        return view('pointages.index', compact('pointages', 'users'));
    }

    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        if (!Auth::user()->hasRole('Admin')) { abort(403); }
        $users = User::all();
        return view('pointages.create', compact('users'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Admin')) { abort(403); }

        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date',
            'heure_depart'    => 'nullable|date|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        $data = $request->only(['user_id', 'date_jour', 'heure_arrivee', 'heure_depart', 'statut_presence']);
        if (!empty($data['heure_arrivee'])) {
            $data['heure_arrivee'] = \Carbon\Carbon::parse($data['heure_arrivee'])->format('Y-m-d H:i:s');
        }
        if (!empty($data['heure_depart'])) {
            $data['heure_depart'] = \Carbon\Carbon::parse($data['heure_depart'])->format('Y-m-d H:i:s');
        }

        DB::transaction(function () use ($data) {
            Pointage::create($data);
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
        if (!Auth::user()->hasRole('Admin')) { abort(403); }

        $pointage = Pointage::with('user')->findOrFail($id);
        $users = User::all();
        return view('pointages.edit', compact('pointage', 'users'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('Admin')) { abort(403); }

        $pointage = Pointage::findOrFail($id);

        $request->validate([
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date',
            'heure_depart'    => 'nullable|date|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        $data = $request->only(['date_jour', 'heure_arrivee', 'heure_depart', 'statut_presence']);
        if (!empty($data['heure_arrivee'])) {
            $data['heure_arrivee'] = \Carbon\Carbon::parse($data['heure_arrivee'])->format('Y-m-d H:i:s');
        }
        if (!empty($data['heure_depart'])) {
            $data['heure_depart'] = \Carbon\Carbon::parse($data['heure_depart'])->format('Y-m-d H:i:s');
        }

        DB::transaction(function () use ($data, $pointage) {
            $pointage->update($data);
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Admin')) { abort(403); }

        $pointage = Pointage::findOrFail($id);

        DB::transaction(function () use ($pointage) {
            $pointage->delete();
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage supprimé avec succès.');
    }

    /**
     * BADGE : ACTION RAPIDE DU WIDGET TABLEAU DE BORD
     * Enregistre l'entrée (store) ou la sortie (update) de l'utilisateur connecté.
     */
    public function badge(Request $request)
    {
        $userId = Auth::id();
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now();

        // Heure limite arbitraire : 9h00 pour décider si c'est "en_retard"
        $heureLimite = Carbon::today()->setHour(9)->setMinute(0)->setSecond(0);

        DB::transaction(function () use ($userId, $today, $now, $heureLimite) {
            $pointage = Pointage::where('user_id', $userId)
                ->where('date_jour', $today)
                ->first();

            if (!$pointage) {
                // Premier badge : Entrée
                $statut = $now->greaterThan($heureLimite) ? 'en_retard' : 'a_l_heure';
                Pointage::create([
                    'user_id'         => $userId,
                    'date_jour'       => $today,
                    'heure_arrivee'   => $now->toDateTimeString(),
                    'heure_depart'    => null,
                    'statut_presence' => $statut,
                ]);
            } else {
                // Deuxième badge : Sortie
                // Heure normale de sortie : 17h00
                $heureSortieNormale = Carbon::today()->setHour(17)->setMinute(0)->setSecond(0);
                if ($now->lessThan($heureSortieNormale)) {
                    $pointage->statut_presence = 'depart_anticipe';
                }
                $pointage->heure_depart = $now->toDateTimeString();
                $pointage->save();
            }
        });

        return redirect()->route('dashboard')->with('success', 'Pointage enregistré avec succès !');
    }
}

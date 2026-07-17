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
    use \App\Http\Controllers\Traits\FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:pointage-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:pointage-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pointage-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pointage-delete', ['only' => ['destroy']]);
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()->hasRole('Super Admin');
    }

    private function isAdmin(): bool
    {
        return Auth::user()->hasRole('Admin');
    }

    private function isRegularEmployee(): bool
    {
        return Auth::user()->hasAnyRole(['Employe_Standard', 'Stagiaire']);
    }

    public function index(Request $request)
    {
        $query = Pointage::with('user');

        if ($this->isSuperAdmin() || $this->isAdmin()) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            $users = $this->excludeSuperAdminsFromUsers(User::orderBy('nom_complet'))->get(['id', 'nom_complet']);
        } else {
            $query->where('user_id', Auth::id());
            $users = collect();
        }

        if ($request->filled('search')) {
            $s = addcslashes($request->search, '%_');
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhere('statut_presence', 'like', "%{$s}%");
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut_presence', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_jour', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_jour', '<=', $request->date_fin);
        }

        $pointages = $query->orderByDesc('date_jour')->paginate(50)->appends($request->query());
        return view('pointages.index', compact('pointages', 'users'));
    }

    public function create()
    {
        if (!($this->isSuperAdmin() || $this->isAdmin())) {
            abort(403, 'Seuls les administrateurs peuvent créer des pointages manuels.');
        }

        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Employe_Standard', 'Stagiaire']);
        })->orderBy('nom_complet')->get(['id', 'nom_complet']);

        return view('pointages.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!($this->isSuperAdmin() || $this->isAdmin())) {
            abort(403, 'Seuls les administrateurs peuvent créer des pointages manuels.');
        }

        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date',
            'heure_depart'    => 'nullable|date|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        $data = $request->only(['user_id', 'date_jour', 'heure_arrivee', 'heure_depart', 'statut_presence']);
        if (!empty($data['heure_arrivee'])) {
            $data['heure_arrivee'] = Carbon::parse($data['heure_arrivee'])->format('Y-m-d H:i:s');
        }
        if (!empty($data['heure_depart'])) {
            $data['heure_depart'] = Carbon::parse($data['heure_depart'])->format('Y-m-d H:i:s');
        }

        $data['created_by'] = Auth::id();

        DB::transaction(function () use ($data) {
            Pointage::create($data);
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage enregistré avec succès.');
    }

    public function show($id)
    {
        $pointage = Pointage::with('user')->findOrFail($id);

        if (!($this->isSuperAdmin() || $this->isAdmin()) && $pointage->user_id != Auth::id()) {
            abort(403);
        }

        return view('pointages.show', compact('pointage'));
    }

    public function edit($id)
    {
        $pointage = Pointage::with('user')->findOrFail($id);

        if ($this->isSuperAdmin()) {
            // Accès total
        } elseif ($this->isAdmin()) {
            if ($pointage->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez modifier que les pointages que vous avez créés.');
            }
        } else {
            abort(403);
        }

        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Employe_Standard', 'Stagiaire']);
        })->orderBy('nom_complet')->get(['id', 'nom_complet']);

        return view('pointages.edit', compact('pointage', 'users'));
    }

    public function update(Request $request, $id)
    {
        $pointage = Pointage::findOrFail($id);

        if ($this->isSuperAdmin()) {
            // Accès total
        } elseif ($this->isAdmin()) {
            if ($pointage->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez modifier que les pointages que vous avez créés.');
            }
        } else {
            abort(403);
        }

        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'date_jour'       => 'required|date',
            'heure_arrivee'   => 'required|date',
            'heure_depart'    => 'nullable|date|after:heure_arrivee',
            'statut_presence' => 'required|in:a_l_heure,en_retard,depart_anticipe',
        ]);

        $data = $request->only(['user_id', 'date_jour', 'heure_arrivee', 'heure_depart', 'statut_presence']);
        if (!empty($data['heure_arrivee'])) {
            $data['heure_arrivee'] = Carbon::parse($data['heure_arrivee'])->format('Y-m-d H:i:s');
        }
        if (!empty($data['heure_depart'])) {
            $data['heure_depart'] = Carbon::parse($data['heure_depart'])->format('Y-m-d H:i:s');
        }

        DB::transaction(function () use ($data, $pointage) {
            $pointage->update($data);
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $pointage = Pointage::findOrFail($id);

        if ($this->isSuperAdmin()) {
            // Accès total
        } elseif ($this->isAdmin()) {
            if ($pointage->created_by !== Auth::id()) {
                abort(403, 'Vous ne pouvez supprimer que les pointages que vous avez créés.');
            }
        } else {
            abort(403);
        }

        DB::transaction(function () use ($pointage) {
            $pointage->delete();
        });

        return redirect()->route('pointages.index')->with('success', 'Pointage supprimé avec succès.');
    }

    public function badge(Request $request)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Le pointage automatique (badge) est réservé aux employés et stagiaires.');
        }

        $userId = Auth::id();
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now();
        $heureLimite = Carbon::today()->setHour(9)->setMinute(0)->setSecond(0);

        DB::transaction(function () use ($userId, $today, $now, $heureLimite) {
            $pointage = Pointage::where('user_id', $userId)
                ->where('date_jour', $today)
                ->first();

            if (!$pointage) {
                $statut = $now->greaterThan($heureLimite) ? 'en_retard' : 'a_l_heure';
                Pointage::create([
                    'user_id'         => $userId,
                    'date_jour'       => $today,
                    'heure_arrivee'   => $now->toDateTimeString(),
                    'heure_depart'    => null,
                    'statut_presence' => $statut,
                    'created_by'      => $userId,
                ]);
            } elseif (is_null($pointage->heure_depart)) {
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

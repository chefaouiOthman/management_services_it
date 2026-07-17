<?php

namespace App\Http\Controllers;

use App\Models\HistoriquePassage;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class HistoriquePassageController extends Controller
{
    use FilterSuperAdmin;
    public function __construct()
    {
        $this->middleware('permission:historique-passage-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:historique-passage-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:historique-passage-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:historique-passage-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Client')) {
                abort(403, 'Accès interdit aux clients.');
            }
            return $next($request);
        });
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = HistoriquePassage::with(['user', 'zone']);

        if ($request->filled('search')) {
            $s = addcslashes($request->search, '%_');
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhereHas('zone', fn ($z) => $z->where('nom_salle', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('horodatage', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('horodatage', '<=', $request->date_fin);
        }

        $historiques = $query->orderByDesc('horodatage')->paginate(25)->appends($request->query());
        return view('historique_passages.index', compact('historiques'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $users = $this->excludeSuperAdminsFromUsers(User::query())->get();
        $zones = Zone::all();
        return view('historique_passages.create', compact('users', 'zones'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'zone_id'          => 'required|exists:zones,id',
            'horodatage'       => 'required|date_format:d/m/Y H:i',
            'tentative_statut' => 'required|in:autorise,refuse_niveau_insuffisant,refuse_zone_desactivee',
        ]);

        $this->validateNotSuperAdminTarget($request);

        $horodatageFormatted = Carbon::createFromFormat('d/m/Y H:i', $request->horodatage)->format('Y-m-d H:i:s');

        DB::transaction(function () use ($request, $horodatageFormatted) {
            HistoriquePassage::create([
                'user_id'          => $request->user_id,
                'zone_id'          => $request->zone_id,
                'horodatage'       => $horodatageFormatted,
                'tentative_statut' => $request->tentative_statut,
            ]);
        });

        return redirect()->route('zones.index')->with('success', 'Historique enregistré avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $historique = HistoriquePassage::with(['user', 'zone'])->findOrFail($id);
        return view('historique_passages.show', compact('historique'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $historique = HistoriquePassage::findOrFail($id);
        $users = $this->excludeSuperAdminsFromUsers(User::query())->get();
        $zones = Zone::all();
        return view('historique_passages.edit', compact('historique', 'users', 'zones'));
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
            'horodatage'       => 'required|date_format:d/m/Y H:i',
            'tentative_statut' => 'required|in:autorise,refuse_niveau_insuffisant,refuse_zone_desactivee',
        ]);

        $this->validateNotSuperAdminTarget($request);

        $horodatageFormatted = Carbon::createFromFormat('d/m/Y H:i', $request->horodatage)->format('Y-m-d H:i:s');

        DB::transaction(function () use ($request, $historique, $horodatageFormatted) {
            $historique->update([
                'user_id'          => $request->user_id,
                'zone_id'          => $request->zone_id,
                'horodatage'       => $horodatageFormatted,
                'tentative_statut' => $request->tentative_statut,
            ]);
        });

        return redirect()->route('zones.index')->with('success', 'Historique mis à jour avec succès.');
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

        return redirect()->route('zones.index')->with('success', 'Historique supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\SessionFormation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class InscriptionController extends Controller
{
    use FilterSuperAdmin;
    public function __construct()
    {
        $this->middleware('permission:inscription-view', ['only' => ['index']]);
        $this->middleware('permission:inscription-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inscription-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inscription-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = Inscription::with(['user', 'sessionFormation.catalogueFormation']);
        
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhereHas('sessionFormation.catalogueFormation', fn ($c) => $c->where('titre_formation', 'like', "%{$s}%"))
                  ->orWhere('statut_inscription', 'like', "%{$s}%");
            });
        }
        if ($request->filled('statut_inscription')) {
            $query->where('statut_inscription', $request->statut_inscription);
        }
        if ($request->filled('session_id')) {
            $query->where('session_formation_id', $request->session_id);
        }

        $inscriptions = $query->get();
        return view('inscriptions.index', compact('inscriptions'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $sessions = SessionFormation::with('catalogueFormation')->get();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        return view('inscriptions.create', compact('sessions', 'users'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
            'user_id'              => 'required|exists:users,id',
            'statut_inscription'   => 'required|in:valide,annule,present,certifie',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403, 'Vous ne pouvez pas inscrire un autre utilisateur.');
        }

        $this->validateNotSuperAdminTarget($request);

        $exists = Inscription::where('user_id', $request->user_id)
            ->where('session_formation_id', $request->session_formation_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'Cet utilisateur est déjà inscrit à cette session.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            Inscription::create([
                'session_formation_id' => $request->session_formation_id,
                'user_id'              => $request->user_id,
                'statut_inscription'   => $request->statut_inscription,
            ]);
        });

        return back()->with('success', 'Inscription réalisée avec succès.');
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $inscription = Inscription::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $inscription->user_id != Auth::id()) {
            abort(403);
        }

        $sessions = SessionFormation::with('catalogueFormation')->get();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        
        return view('inscriptions.edit', compact('inscription', 'sessions', 'users'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $inscription = Inscription::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $inscription->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
            'user_id'              => 'required|exists:users,id',
            'statut_inscription'   => 'required|in:valide,annule,present,certifie',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403);
        }

        $this->validateNotSuperAdminTarget($request);

        $exists = Inscription::where('user_id', $request->user_id)
            ->where('session_formation_id', $request->session_formation_id)
            ->where('id', '!=', $inscription->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'Cet utilisateur est déjà inscrit à cette session.'])->withInput();
        }

        DB::transaction(function () use ($request, $inscription) {
            $inscription->update([
                'session_formation_id' => $request->session_formation_id,
                'user_id'              => $request->user_id,
                'statut_inscription'   => $request->statut_inscription,
            ]);
        });

        return redirect()->route('inscriptions.index')->with('success', 'Inscription mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $inscription = Inscription::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $inscription->user_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($inscription) {
            $inscription->delete();
        });

        return redirect()->route('inscriptions.index')->with('success', 'Inscription supprimée avec succès.');
    }

    /**
     * 8. UPDATE STATUT (Asynchrone Alpine Fetch)
     */
    public function updateStatut(Request $request, Inscription $inscription)
    {
        $request->validate([
            'statut_inscription' => 'required|in:valide,annule,present,certifie',
        ]);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('inscription-edit')) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        DB::transaction(function () use ($request, $inscription) {
            $inscription->update([
                'statut_inscription' => $request->statut_inscription,
            ]);
        });

        return response()->json([
            'success'            => true,
            'statut_inscription' => $request->statut_inscription,
            'message'            => 'Statut mis à jour.',
        ]);
    }
}

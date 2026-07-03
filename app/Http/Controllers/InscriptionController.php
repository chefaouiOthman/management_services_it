<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\SessionFormation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inscription-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inscription-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inscription-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inscription-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $query = Inscription::with(['user', 'sessionFormation.catalogueFormation']);
        
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('user_id', Auth::id());
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
        $users = Auth::user()->hasRole('Admin') ? User::all() : collect([Auth::user()]);
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

        DB::transaction(function () use ($request) {
            Inscription::create([
                'session_formation_id' => $request->session_formation_id,
                'user_id'              => $request->user_id,
                'statut_inscription'   => $request->statut_inscription,
            ]);
        });

        return redirect()->route('inscriptions.index')->with('success', 'Inscription réalisée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $inscription = Inscription::with(['user', 'sessionFormation'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $inscription->user_id != Auth::id()) {
            abort(403);
        }

        return view('inscriptions.show', compact('inscription'));
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
        $users = Auth::user()->hasRole('Admin') ? User::all() : collect([Auth::user()]);
        
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
}

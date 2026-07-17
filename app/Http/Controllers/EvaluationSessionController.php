<?php

namespace App\Http\Controllers;

use App\Models\EvaluationSession;
use App\Models\SessionFormation;
use App\Models\User;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EvaluationSessionController extends Controller
{
    use \App\Http\Controllers\Traits\FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:evaluation-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:evaluation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:evaluation-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:evaluation-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX — optionnellement filtré par session
     */
    public function index(Request $request)
    {
        $session    = null;

        if ($request->filled('session')) {
            $session = SessionFormation::with(['catalogueFormation'])->findOrFail($request->session);
            $query = EvaluationSession::with(['user', 'formateur.user'])
                         ->where('session_formation_id', $session->id);
        } else {
            $query = EvaluationSession::with(['sessionFormation.catalogueFormation', 'user', 'formateur.user']);
            if (!Auth::user()->hasRole('Admin')) {
                $query->where('user_id', Auth::id())
                      ->orWhere('employe_id', Auth::id());
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhere(function($q) use ($s) { if (is_numeric($s)) $q->where('note_technique', $s); })
                  ->orWhereHas('formateur.user', fn ($f) => $f->where('nom_complet', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('note_min')) {
            $query->where('note_technique', '>=', $request->note_min);
        }

        $evaluations = $query->get();
        return view('evaluations.index', compact('evaluations', 'session'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $sessions = SessionFormation::all();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        $formateurs = $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get();
        return view('evaluations.create', compact('sessions', 'users', 'formateurs'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
            'user_id'              => 'required|exists:users,id',
            'employe_id'           => 'required|exists:employes,user_id',
            'note_pedagogie'       => 'required|integer|min:0|max:10',
            'note_technique'       => 'required|integer|min:0|max:10',
            'avis_textuel'         => 'nullable|string',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403, 'Vous ne pouvez pas évaluer à la place d\'un autre utilisateur.');
        }

        DB::transaction(function () use ($request) {
            EvaluationSession::create([
                'session_formation_id' => $request->session_formation_id,
                'user_id'              => $request->user_id,
                'employe_id'           => $request->employe_id,
                'note_pedagogie'       => $request->note_pedagogie,
                'note_technique'       => $request->note_technique,
                'avis_textuel'         => $request->avis_textuel,
            ]);
        });

        return redirect()->route('evaluations.index')->with('success', 'Évaluation enregistrée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $evaluation = EvaluationSession::with(['sessionFormation', 'user', 'employe.user'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $evaluation->user_id != Auth::id() && $evaluation->employe_id != Auth::id()) {
            abort(403);
        }

        return view('evaluations.show', compact('evaluation'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $evaluation = EvaluationSession::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $evaluation->user_id != Auth::id()) {
            abort(403);
        }

        $sessions = SessionFormation::all();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        $formateurs = $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get();
        return view('evaluations.edit', compact('evaluation', 'sessions', 'users', 'formateurs'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $evaluation = EvaluationSession::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $evaluation->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
            'user_id'              => 'required|exists:users,id',
            'employe_id'           => 'required|exists:employes,user_id',
            'note_pedagogie'       => 'required|integer|min:0|max:10',
            'note_technique'       => 'required|integer|min:0|max:10',
            'avis_textuel'         => 'nullable|string',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($request, $evaluation) {
            $evaluation->update([
                'session_formation_id' => $request->session_formation_id,
                'user_id'              => $request->user_id,
                'employe_id'           => $request->employe_id,
                'note_pedagogie'       => $request->note_pedagogie,
                'note_technique'       => $request->note_technique,
                'avis_textuel'         => $request->avis_textuel,
            ]);
        });

        return redirect()->route('evaluations.index')->with('success', 'Évaluation mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $evaluation = EvaluationSession::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $evaluation->user_id != Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($evaluation) {
            $evaluation->delete();
        });

        return redirect()->route('evaluations.index')->with('success', 'Évaluation supprimée avec succès.');
    }
}

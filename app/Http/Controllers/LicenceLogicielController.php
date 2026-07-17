<?php

namespace App\Http\Controllers;

use App\Models\LicenceLogiciel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LicenceLogicielController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->hasRole('Client')) abort(403, 'Accès interdit aux clients.');
            return $next($request);
        });
        $this->middleware('permission:licence-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:licence-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:licence-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:licence-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = LicenceLogiciel::query();

        if (!Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $query->whereHas('assignations', fn ($q) => $q->where('user_id', Auth::id())->whereNull('date_revocation'));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom_logiciel', 'like', "%{$s}%")
                  ->orWhere('date_expiration', 'like', "%{$s}%")
                  ->orWhere('cle_licence', 'like', "%{$s}%");
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $licences = $query->paginate(25)->appends($request->query());
        return view('licences.index', compact('licences'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $this->denyInventaireMutation();

        return view('licences.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $this->denyInventaireMutation();

        $request->validate([
            'nom_logiciel'    => 'required|string|max:100',
            'cle_licence'     => 'required|string|max:255',
            'date_expiration' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            LicenceLogiciel::create([
                'nom_logiciel'    => $request->nom_logiciel,
                'cle_licence'     => $request->cle_licence,
                'date_expiration' => $request->date_expiration,
            ]);
        });

        return redirect()->route('licences.index')->with('success', 'Licence ajoutée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $licence = LicenceLogiciel::with('assignations')->findOrFail($id);

        if (!Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $hasAccess = $licence->assignations->contains(fn ($a) => $a->user_id === Auth::id() && $a->date_revocation === null);
            if (!$hasAccess) abort(403);
        }

        return view('licences.show', compact('licence'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $this->denyInventaireMutation();

        $licence = LicenceLogiciel::findOrFail($id);
        return view('licences.edit', compact('licence'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $this->denyInventaireMutation();

        $licence = LicenceLogiciel::findOrFail($id);

        $request->validate([
            'nom_logiciel'    => 'required|string|max:100',
            'cle_licence'     => 'required|string|max:255',
            'date_expiration' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $licence) {
            $licence->update([
                'nom_logiciel'    => $request->nom_logiciel,
                'cle_licence'     => $request->cle_licence,
                'date_expiration' => $request->date_expiration,
            ]);
        });

        return redirect()->route('licences.index')->with('success', 'Licence mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $this->denyInventaireMutation();

        $licence = LicenceLogiciel::findOrFail($id);

        DB::transaction(function () use ($licence) {
            $licence->delete();
        });

        return redirect()->route('licences.index')->with('success', 'Licence supprimée avec succès.');
    }
}

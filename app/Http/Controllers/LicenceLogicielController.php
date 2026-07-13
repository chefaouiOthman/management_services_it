<?php

namespace App\Http\Controllers;

use App\Models\LicenceLogiciel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicenceLogicielController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Client')) {
                abort(403, 'Accès interdit.');
            }
            return $next($request);
        }, ['only' => ['index', 'show']]);

        $this->middleware('permission:licence-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:licence-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:licence-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $licences = LicenceLogiciel::all();
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
        $licence = LicenceLogiciel::findOrFail($id);
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

<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:zone-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:zone-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:zone-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:zone-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $zones = Zone::all();
        return view('zones.index', compact('zones'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('zones.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'code_zone'     => 'required|string|max:50|unique:zones,code_zone',
            'nom_salle'     => 'required|string|max:100',
            'niveau_requis' => 'required|integer|min:0',
            'est_active'    => 'boolean',
        ]);

        DB::transaction(function () use ($request) {
            Zone::create([
                'code_zone'     => $request->code_zone,
                'nom_salle'     => $request->nom_salle,
                'niveau_requis' => $request->niveau_requis,
                'est_active'    => $request->input('est_active', true),
            ]);
        });

        return redirect()->route('zones.index')->with('success', 'Zone créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $zone = Zone::findOrFail($id);
        return view('zones.show', compact('zone'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        return view('zones.edit', compact('zone'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $request->validate([
            'code_zone'     => ['required', 'string', 'max:50', Rule::unique('zones')->ignore($zone->id)],
            'nom_salle'     => 'required|string|max:100',
            'niveau_requis' => 'required|integer|min:0',
            'est_active'    => 'boolean',
        ]);

        DB::transaction(function () use ($request, $zone) {
            $zone->update([
                'code_zone'     => $request->code_zone,
                'nom_salle'     => $request->nom_salle,
                'niveau_requis' => $request->niveau_requis,
                'est_active'    => $request->has('est_active') ? $request->est_active : $zone->est_active,
            ]);
        });

        return redirect()->route('zones.index')->with('success', 'Zone mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);

        DB::transaction(function () use ($zone) {
            $zone->delete();
        });

        return redirect()->route('zones.index')->with('success', 'Zone supprimée avec succès.');
    }
}

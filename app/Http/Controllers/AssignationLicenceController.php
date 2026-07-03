<?php

namespace App\Http\Controllers;

use App\Models\AssignationLicence;
use App\Models\LicenceLogiciel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignationLicenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:assignation-licence-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:assignation-licence-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:assignation-licence-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:assignation-licence-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $assignations = AssignationLicence::with(['user', 'licenceLogiciel'])->get();
        return view('assignation_licences.index', compact('assignations'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $users = User::all();
        $licences = LicenceLogiciel::all();
        return view('assignation_licences.create', compact('users', 'licences'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'licence_logiciel_id' => 'required|exists:licence_logiciels,id',
            'date_attribution'    => 'required|date',
            'date_revocation'     => 'nullable|date|after_or_equal:date_attribution',
        ]);

        DB::transaction(function () use ($request) {
            AssignationLicence::create([
                'user_id'             => $request->user_id,
                'licence_logiciel_id' => $request->licence_logiciel_id,
                'date_attribution'    => $request->date_attribution,
                'date_revocation'     => $request->date_revocation,
            ]);
        });

        return redirect()->route('assignation_licences.index')->with('success', 'Attribution de licence créée avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $assignation = AssignationLicence::with(['user', 'licenceLogiciel'])->findOrFail($id);
        return view('assignation_licences.show', compact('assignation'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $assignation = AssignationLicence::findOrFail($id);
        $users = User::all();
        $licences = LicenceLogiciel::all();
        return view('assignation_licences.edit', compact('assignation', 'users', 'licences'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $assignation = AssignationLicence::findOrFail($id);

        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'licence_logiciel_id' => 'required|exists:licence_logiciels,id',
            'date_attribution'    => 'required|date',
            'date_revocation'     => 'nullable|date|after_or_equal:date_attribution',
        ]);

        DB::transaction(function () use ($request, $assignation) {
            $assignation->update([
                'user_id'             => $request->user_id,
                'licence_logiciel_id' => $request->licence_logiciel_id,
                'date_attribution'    => $request->date_attribution,
                'date_revocation'     => $request->date_revocation,
            ]);
        });

        return redirect()->route('assignation_licences.index')->with('success', 'Attribution de licence mise à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $assignation = AssignationLicence::findOrFail($id);

        DB::transaction(function () use ($assignation) {
            $assignation->delete();
        });

        return redirect()->route('assignation_licences.index')->with('success', 'Attribution de licence supprimée avec succès.');
    }
}

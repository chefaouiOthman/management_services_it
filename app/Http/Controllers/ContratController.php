<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    use \App\Http\Controllers\Traits\FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:contrat-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:contrat-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:contrat-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:contrat-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = Contrat::with('employe.user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('type_contrat', 'like', "%{$s}%")
                  ->orWhere('salaire_base', 'like', "%{$s}%")
                  ->orWhereHas('employe.user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->paginate(25)->appends($request->query());
        return view('contrats.index', compact('contrats'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $employes = $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get();
        return view('contrats.create', compact('employes'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'type_contrat' => 'required|in:CDI,CDD,Freelance',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut|required_if:type_contrat,CDD,Freelance',
            'salaire_base' => 'required|numeric|min:0',
            'heures_hebdo' => 'required|integer|min:0',
            'statut'       => 'required|in:actif,suspendu,termine',
        ]);

        DB::transaction(function () use ($request) {
            Contrat::create([
                'employe_id'   => $request->employe_id,
                'type_contrat' => $request->type_contrat,
                'date_debut'   => $request->date_debut,
                'date_fin'     => $request->date_fin,
                'salaire_base' => $request->salaire_base,
                'heures_hebdo' => $request->heures_hebdo,
                'statut'       => $request->statut,
            ]);
        });

        return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $contrat = Contrat::with('employe.user')->findOrFail($id);
        return view('contrats.show', compact('contrat'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $contrat = Contrat::findOrFail($id);
        $employes = $this->excludeSuperAdminsFromEmployes(Employe::with('user'))->get();
        return view('contrats.edit', compact('contrat', 'employes'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $contrat = Contrat::findOrFail($id);

        $request->validate([
            'employe_id'   => 'required|exists:employes,user_id',
            'type_contrat' => 'required|in:CDI,CDD,Freelance',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut|required_if:type_contrat,CDD,Freelance',
            'salaire_base' => 'required|numeric|min:0',
            'heures_hebdo' => 'required|integer|min:0',
            'statut'       => 'required|in:actif,suspendu,termine',
        ]);

        DB::transaction(function () use ($request, $contrat) {
            $contrat->update([
                'employe_id'   => $request->employe_id,
                'type_contrat' => $request->type_contrat,
                'date_debut'   => $request->date_debut,
                'date_fin'     => $request->date_fin,
                'salaire_base' => $request->salaire_base,
                'heures_hebdo' => $request->heures_hebdo,
                'statut'       => $request->statut,
            ]);
        });

        return redirect()->route('contrats.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);

        DB::transaction(function () use ($contrat) {
            $contrat->delete();
        });

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé avec succès.');
    }
}

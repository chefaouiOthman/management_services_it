<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\User;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class EmployeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employe-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:employe-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:employe-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:employe-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES EMPLOYES
     */
    public function index()
    {
        $employes = Employe::with(['user', 'contrat'])->paginate(50);
        return view('employes.index', compact('employes'));
    }

    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'Employe_Standard'])->get();
        return view('employes.create', compact('roles'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_complet'     => 'required|string|max:150',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8',
            'est_actif'       => 'boolean',
            'CIN'             => 'required|string|max:50|unique:employes',
            'date_embauche'   => 'required|date',
            'type_contrat'    => 'required|in:CDI,CDD,Freelance',
            'date_debut'      => 'required|date',
            'date_fin'        => 'nullable|date|after_or_equal:date_debut',
            'salaire_base'    => 'required|numeric|min:0',
            'heures_hebdo'    => 'required|integer|min:0',
            'statut'          => 'required|in:actif,suspendu,termine',
            'role'            => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'est_actif'   => $request->input('est_actif', true),
            ]);

            $employe = Employe::create([
                'user_id'        => $user->id,
                'CIN'            => $request->CIN,
                'date_embauche'  => $request->date_embauche,
            ]);

            Contrat::create([
                'employe_id'    => $employe->user_id,
                'type_contrat'  => $request->type_contrat,
                'date_debut'    => $request->date_debut,
                'date_fin'      => $request->date_fin,
                'salaire_base'  => $request->salaire_base,
                'heures_hebdo'  => $request->heures_hebdo,
                'statut'        => $request->statut,
            ]);

            $user->assignRole($request->role);
        });

        return redirect()->route('employes.index')->with('success', 'Employé créé avec succès !');
    }

    /**
     * 4. SHOW : AFFICHER UN EMPLOYE
     */
    public function show($id)
    {
        $employe = Employe::with(['user', 'contrat'])->findOrFail($id);
        return view('employes.show', compact('employe'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        $employe = Employe::with(['user', 'contrat'])->findOrFail($id);
        $roles = Role::whereIn('name', ['Admin', 'Employe_Standard'])->get();
        return view('employes.edit', compact('employe', 'roles'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        $employe = Employe::findOrFail($id);
        $user = $employe->user;

        $request->validate([
            'nom_complet'     => 'required|string|max:150',
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'        => 'nullable|string|min:8',
            'est_actif'       => 'boolean',
            'CIN'             => ['required', 'string', 'max:50', Rule::unique('employes')->ignore($employe->user_id, 'user_id')],
            'date_embauche'   => 'required|date',
            'type_contrat'    => 'required|in:CDI,CDD,Freelance',
            'date_debut'      => 'required|date',
            'date_fin'        => 'nullable|date|after_or_equal:date_debut',
            'salaire_base'    => 'required|numeric|min:0',
            'heures_hebdo'    => 'required|integer|min:0',
            'statut'          => 'required|in:actif,suspendu,termine',
            'role'            => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user, $employe) {
            $user->update([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'est_actif'   => $request->has('est_actif') ? $request->est_actif : $user->est_actif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $employe->update([
                'CIN'           => $request->CIN,
                'date_embauche' => $request->date_embauche,
            ]);

            if ($employe->contrat) {
                $employe->contrat->update([
                    'type_contrat'  => $request->type_contrat,
                    'date_debut'    => $request->date_debut,
                    'date_fin'      => $request->date_fin,
                    'salaire_base'  => $request->salaire_base,
                    'heures_hebdo'  => $request->heures_hebdo,
                    'statut'        => $request->statut,
                ]);
            } else {
                Contrat::create([
                    'employe_id'    => $employe->user_id,
                    'type_contrat'  => $request->type_contrat,
                    'date_debut'    => $request->date_debut,
                    'date_fin'      => $request->date_fin,
                    'salaire_base'  => $request->salaire_base,
                    'heures_hebdo'  => $request->heures_hebdo,
                    'statut'        => $request->statut,
                ]);
            }

            $user->syncRoles([$request->role]);
        });

        return redirect()->route('employes.index')->with('success', 'Employé mis à jour avec succès !');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        $employe = Employe::findOrFail($id);
        // Cascade removes Employe and Contrat
        $employe->user->delete();

        return redirect()->route('employes.index')->with('success', 'Employé supprimé avec succès.');
    }
}

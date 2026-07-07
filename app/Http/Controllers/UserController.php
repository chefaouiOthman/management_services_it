<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES UTILISATEURS
     */
    public function index()
    {
        $users = User::with(['roles', 'employe.contrats', 'employe.departement', 'stagiaire.departement', 'client'])->paginate(25);
        return view('users.index', compact('users'));
    }


    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:150',
            'email'       => 'required|string|email|max:255|unique:users',
            'cin'         => 'nullable|string|max:50|unique:users,cin',
            'password'    => 'required|string|min:8',
            'est_actif'   => 'boolean',
            'roles'       => 'nullable|array',
            'roles.*'     => 'exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'cin'         => $request->cin,
                'password'    => Hash::make($request->password),
                'est_actif'   => $request->input('est_actif', true),
            ]);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
        });

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * 4. SHOW : AFFICHER UN UTILISATEUR
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        $user = User::with(['employe', 'stagiaire', 'client'])->findOrFail($id);

        $request->validate([
            'nom_complet'    => 'required|string|max:150',
            'email'          => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cin'            => ['nullable', 'string', 'max:50', Rule::unique('users', 'cin')->ignore($user->id)],
            'password'       => 'nullable|string|min:8',
            'est_actif'      => 'boolean',
            'roles'          => 'nullable|array',
            'roles.*'        => 'exists:roles,name',
            'departement_id' => 'nullable|exists:departements,id',
            'date_embauche'  => 'nullable|date',
            'CIN'            => 'nullable|string|max:50',
            'ecole_origine'  => 'nullable|string|max:150',
            'sujet_stage'    => 'nullable|string',
            'type_client'    => 'nullable|in:physique,morale',
            'nom_societe'    => 'nullable|string|max:150',
            'ice'            => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Mise à jour du User parent
            $user->update([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'cin'         => $request->cin,
                'est_actif'   => $request->has('est_actif') ? (bool)$request->est_actif : $user->est_actif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles([]);
            }

            // 2. Mise à jour polymorphique de l'entité fille
            if ($user->employe) {
                $user->employe->update(array_filter([
                    'departement_id' => $request->departement_id,
                    'date_embauche'  => $request->date_embauche ?: $user->employe->date_embauche,
                    'CIN'            => $request->CIN ?: $user->employe->CIN,
                ], fn($v) => !is_null($v)));
            } elseif ($user->stagiaire) {
                $user->stagiaire->update(array_filter([
                    'departement_id' => $request->departement_id,
                    'ecole_origine'  => $request->ecole_origine ?: $user->stagiaire->ecole_origine,
                    'sujet_stage'    => $request->sujet_stage ?: $user->stagiaire->sujet_stage,
                ], fn($v) => !is_null($v)));
            } elseif ($user->client) {
                $user->client->update(array_filter([
                    'type_client'  => $request->type_client ?: $user->client->type_client,
                    'nom_societe'  => $request->nom_societe,
                    'ice'          => $request->ice,
                ], fn($v) => !is_null($v)));
            }
        });

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}

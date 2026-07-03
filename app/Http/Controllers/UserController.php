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
        $users = User::with('roles')->get();
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
            'password'    => 'required|string|min:8',
            'est_actif'   => 'boolean',
            'roles'       => 'nullable|array',
            'roles.*'     => 'exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
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
        $user = User::findOrFail($id);

        $request->validate([
            'nom_complet' => 'required|string|max:150',
            'email'       => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:8',
            'est_actif'   => 'boolean',
            'roles'       => 'nullable|array',
            'roles.*'     => 'exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'est_actif'   => $request->has('est_actif') ? $request->est_actif : $user->est_actif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles([]);
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

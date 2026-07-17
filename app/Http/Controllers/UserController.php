<?php

namespace App\Http\Controllers;

use App\Mail\UserCredentialsMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use \App\Http\Controllers\Traits\FilterSuperAdmin;

    public function __construct()
    {
        $this->middleware('permission:user-view', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES UTILISATEURS
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return redirect()->route('users.show', auth()->id());
        }

        $query = User::with([
            'roles',
            'employe.departement',
            'employe.contrats' => fn ($q) => $q->orderByDesc('id'),
            'stagiaire.departement',
            'client',
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom_complet', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $this->excludeSuperAdminsFromUsers($query);

        if ($request->has('role') && !empty($request->role)) {
            $role = $request->role;
            if ($role === 'employe') {
                $query->whereHas('employe');
            } elseif ($role === 'stagiaire') {
                $query->whereHas('stagiaire');
            } elseif ($role === 'client') {
                $query->whereHas('client');
            }
        }

        $users = $query->paginate(25)->appends($request->query());
        return view('users.index', compact('users'));
    }


    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        $roles = Role::all();
        if (!auth()->user()->hasRole('Super Admin')) {
            $roles = $roles->reject(fn($r) => $r->name === 'Super Admin');
        }
        $user = new User();
        return view('users.create', compact('roles', 'user'));
    }

    /**
     * 3. STORE : ENREGISTREMENT EN BASE DE DONNEES
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Super Admin') && in_array('Super Admin', $request->roles ?? [])) {
            abort(403, 'Seul le Super Admin peut attribuer le rôle Super Admin.');
        }

        $isEmployeeRole = $request->has('roles') && (
            in_array('Employe_Standard', $request->roles) || in_array('Admin', $request->roles)
        );

        $request->validate([
            'nom_complet'    => 'required|string|max:150',
            'email'          => 'required|string|email|max:255|unique:users',
            'cin'            => 'nullable|string|max:50|unique:users,cin',
            'est_actif'      => 'boolean',
            'roles'          => 'nullable|array',
            'roles.*'        => 'exists:roles,name',
            'departement_id' => 'nullable|exists:departements,id',
            'date_embauche'  => 'nullable|date',
            'ecole_origine'  => 'nullable|string|max:150',
            'sujet_stage'    => 'nullable|string',
            'type_client'    => 'nullable|in:physique,morale',
            'nom_societe'    => 'nullable|string|max:150',
            'ice'            => 'nullable|string|max:50',
            'type_contrat'   => $isEmployeeRole ? 'required|in:CDI,CDD,Freelance' : 'nullable|in:CDI,CDD,Freelance',
            'date_debut'     => $isEmployeeRole ? 'required|date' : 'nullable|date',
            'date_fin'       => $isEmployeeRole ? 'nullable|date|required_if:type_contrat,CDD,Freelance' : 'nullable|date',
            'salaire_base'   => $isEmployeeRole ? 'required|numeric' : 'nullable|numeric',
            'heures_hebdo'   => $isEmployeeRole ? 'required|integer' : 'nullable|integer',
            'statut'         => $isEmployeeRole ? 'required|in:actif,suspendu,termine' : 'nullable|in:actif,suspendu,termine',
        ]);

        $plainPassword = Str::password(12);

        DB::transaction(function () use ($request, &$user, $plainPassword) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'cin'         => $request->cin,
                'password'    => Hash::make($plainPassword),
                'est_actif'   => $request->input('est_actif', true),
            ]);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
                
                // Create child entity based on role
                if (in_array('Employe_Standard', $request->roles) || in_array('Admin', $request->roles)) {
                    $employe = $user->employe()->create([
                        'departement_id' => $request->departement_id,
                        'date_embauche'  => $request->date_embauche ?: now(),
                    ]);
                    
                    $employe->contrats()->create([
                        'type_contrat' => $request->type_contrat,
                        'date_debut'   => $request->date_debut,
                        'date_fin'     => $request->date_fin,
                        'salaire_base' => $request->salaire_base,
                        'heures_hebdo' => $request->heures_hebdo,
                        'statut'       => $request->statut ?? 'actif',
                    ]);
                } elseif (in_array('Stagiaire', $request->roles)) {
                    $user->stagiaire()->create([
                        'departement_id' => $request->input('departement_id') ?: null,
                        'ecole_origine' => $request->input('ecole_origine'),
                        'sujet_stage' => $request->input('sujet_stage'),
                    ]);
                } elseif (in_array('Client', $request->roles)) {
                    $user->client()->create([
                        'type_client'  => $request->input('type_client') ?: 'physique',
                        'nom_societe'  => $request->input('nom_societe'),
                        'ice'          => $request->input('ice'),
                    ]);
                }
            }
        });

        Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * 4. SHOW : AFFICHER UN UTILISATEUR
     */
    public function show($id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && auth()->id() != $id) {
            abort(403);
        }

        $user = User::with([
            'roles',
            'employe.departement',
            'employe.contrats' => fn ($q) => $q->orderByDesc('id'),
            'stagiaire.departement',
            'client',
        ])->findOrFail($id);

        $this->abortIfTargetIsSuperAdmin($user);

        return view('users.show', compact('user'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403);
        }

        $user = User::with([
            'roles',
            'employe.departement',
            'employe.contrats' => fn ($q) => $q->orderByDesc('id'),
            'stagiaire.departement',
            'client',
        ])->findOrFail($id);
        $this->abortIfTargetIsSuperAdmin($user);
        $roles = Role::all();
        if (!auth()->user()->hasRole('Super Admin')) {
            $roles = $roles->reject(fn($r) => $r->name === 'Super Admin');
        }
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403);
        }

        $user = User::with(['employe', 'stagiaire', 'client'])->findOrFail($id);

        $this->abortIfTargetIsSuperAdmin($user);

        if (!auth()->user()->hasRole('Super Admin') && in_array('Super Admin', $request->roles ?? [])) {
            abort(403, 'Seul le Super Admin peut attribuer le rôle Super Admin.');
        }

        $request->validate([
            'nom_complet'    => 'required|string|max:150',
            'email'          => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cin'            => ['nullable', 'string', 'max:50', Rule::unique('users', 'cin')->ignore($user->id)],
            'est_actif'      => 'boolean',
            'roles'          => 'nullable|array',
            'roles.*'        => 'exists:roles,name',
            'departement_id' => 'nullable|exists:departements,id',
            'date_embauche'  => 'nullable|date',
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

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles([]);
            }

            // 2. Mise à jour polymorphique de l'entité fille
           if ($user->employe) {
                $user->employe->update([
                    'departement_id' => $request->departement_id,
                    'date_embauche'  => $request->date_embauche ?? $user->employe->date_embauche,
                ]);
            }
         elseif ($user->stagiaire) {
                $user->stagiaire->update([
                    'departement_id' => $request->departement_id,
                    'ecole_origine'  => $request->ecole_origine ?? $user->stagiaire->ecole_origine,
                    'sujet_stage'    => $request->sujet_stage ?? $user->stagiaire->sujet_stage,
                ]);
            } elseif ($user->client) {
                $user->client->update([
                    'type_client'  => $request->input('type_client') ?: $user->client->type_client,
                    'nom_societe'  => $request->input('nom_societe'),
                    'ice'          => $request->input('ice'),
                ]);
            }

            // Handle role changes - create new entity if role changed to employee/stagiaire/client
            if ($request->has('roles')) {
                if ((in_array('Employe_Standard', $request->roles) || in_array('Admin', $request->roles)) && !$user->employe) {
                    $employe = $user->employe()->create([
                        'departement_id' => $request->input('departement_id') ?: null,
                        'date_embauche'  => $request->input('date_embauche') ?: now(),
                    ]);
                    $employe->contrats()->create([
                        'type_contrat' => $request->type_contrat ?? 'CDI',
                        'date_debut'   => $request->date_debut ?? now(),
                        'date_fin'     => $request->date_fin,
                        'salaire_base' => $request->salaire_base ?? 0,
                        'heures_hebdo' => $request->heures_hebdo ?? 40,
                        'statut'       => 'actif',
                    ]);
                } elseif (in_array('Stagiaire', $request->roles) && !$user->stagiaire) {
                    $user->stagiaire()->create([
                        'departement_id' => $request->input('departement_id') ?: null,
                        'ecole_origine'  => $request->input('ecole_origine'),
                        'sujet_stage'    => $request->input('sujet_stage'),
                    ]);
                } elseif (in_array('Client', $request->roles) && !$user->client) {
                    $user->client()->create([
                        'type_client'  => $request->input('type_client') ?: 'physique',
                        'nom_societe'  => $request->input('nom_societe'),
                        'ice'          => $request->input('ice'),
                    ]);
                }
            }
        });

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403);
        }

        $user = User::with([
            'employe',
            'stagiaire', 
            'client',
            'pointages',
            'historiquePassages',
            'inscriptions',
            'evaluationsSession',
            'assignationsMateriel',
            'assignationsLicence',
            'ticketsMaintenance',
        ])->findOrFail($id);

        $this->abortIfTargetIsSuperAdmin($user);

        DB::transaction(function () use ($user) {
            // Delete child entities explicitly
            if ($user->employe) {
                $user->employe->delete();
            }
            if ($user->stagiaire) {
                $user->stagiaire->delete();
            }
            if ($user->client) {
                $user->client->delete();
            }

            // Delete related records that don't have cascade deletes
            $user->pointages()->delete();
            $user->historiquePassages()->delete();
            $user->inscriptions()->delete();
            $user->evaluationsSession()->delete();
            $user->assignationsMateriel()->delete();
            $user->assignationsLicence()->delete();
            $user->ticketsMaintenance()->delete();

            // Delete the user
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}

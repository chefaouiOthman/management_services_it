<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('Super Admin')) {
                abort(403, 'Action non autorisée. Seul le Super Administrateur peut gérer les rôles.');
            }
            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%");
            });
        }

        $roles = $query->paginate(25)->appends($request->query());
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = collect();
        return view('roles.create', compact('permissions', 'rolePermissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission' => 'nullable|array',
            'permission.*' => 'exists:permissions,name',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permission')) {
            $role->syncPermissions($request->permission);
        }

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');
    }

    public function show($id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    public function edit($id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permission' => 'nullable|array',
            'permission.*' => 'exists:permissions,name',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role->update(['name' => $request->name]);

        if ($request->has('permission')) {
            $role->syncPermissions($request->permission);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy($id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Le rôle Super Admin ne peut pas être supprimé.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');
    }
}

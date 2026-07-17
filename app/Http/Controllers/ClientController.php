<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:client-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:client-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:client-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:client-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX : LISTE DES CLIENTS
     */
    public function index(Request $request)
    {
        $query = Client::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('nom_complet', 'like', "%{$s}%"))
                  ->orWhere('nom_societe', 'like', "%{$s}%");
            });
        }

        $clients = $query->paginate(25)->appends($request->query());
        return view('clients.index', compact('clients'));
    }

    /**
     * 2. CREATE : FORMULAIRE DE CREATION
     */
    public function create()
    {
        $client = new Client();
        return view('clients.create', compact('client'));
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
            'type_client' => 'required|in:physique,morale',
            'nom_societe' => 'nullable|string|max:150',
            'ice'         => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'est_actif'   => $request->input('est_actif', true),
            ]);

            Client::create([
                'user_id'      => $user->id,
                'type_client'  => $request->type_client,
                'nom_societe'  => $request->nom_societe,
                'ice'          => $request->ice,
            ]);

            $user->assignRole('Client');
        });

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * 4. SHOW : AFFICHER UN CLIENT
     */
    public function show($id)
    {
        $client = Client::with('user')->findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * 5. EDIT : FORMULAIRE DE MISE A JOUR
     */
    public function edit($id)
    {
        $client = Client::with('user')->findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * 6. UPDATE : MISE A JOUR EN BASE DE DONNEES
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $user = $client->user;

        $request->validate([
            'nom_complet' => 'required|string|max:150',
            'email'       => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:8',
            'est_actif'   => 'boolean',
            'type_client' => 'required|in:physique,morale',
            'nom_societe' => 'nullable|string|max:150',
            'ice'         => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $user, $client) {
            $user->update([
                'nom_complet' => $request->nom_complet,
                'email'       => $request->email,
                'est_actif'   => $request->has('est_actif') ? $request->est_actif : $user->est_actif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $client->update([
                'type_client'  => $request->type_client,
                'nom_societe'  => $request->nom_societe,
                'ice'          => $request->ice,
            ]);
        });

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * 7. DESTROY : SUPPRESSION DEFINITIVE
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        if ($client->user) { $client->user?->delete(); }

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Livrable;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LivrableController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:livrable-view', ['only' => ['index', 'show', 'download']]);
        $this->middleware('permission:livrable-create', ['only' => ['create', 'store', 'createForProject', 'storeForProject']]);
        $this->middleware('permission:livrable-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:livrable-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $livrables = Livrable::with('projet')->orderByDesc('date_limite_soumission')->get();
        return view('livrables.index', compact('livrables'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $projets = Projet::all();
        return view('livrables.create', compact('projets'));
    }

    /**
     * 2b. CREATE NESTED (Project-scoped)
     */
    public function createForProject(Projet $projet)
    {
        return view('livrables.create', compact('projet'));
    }

    /**
     * 3. STORE — avec gestion de l'upload de fichier
     */
    public function store(Request $request)
    {
        $request->validate([
            'projet_id'              => 'required|exists:projets,id',
            'titre_jalon'            => 'required|string|max:150',
            'date_limite_soumission' => 'required|date',
            'statut_client'          => 'required|in:en_attente,rejete_avec_corrections,valide',
            'fichier'                => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:20480',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            $nomOriginal = null;

            if ($request->hasFile('fichier')) {
                $file = $request->file('fichier');
                $path = $file->store('livrables', 'private');
                $nomOriginal = $file->getClientOriginalName();
            }

            Livrable::create([
                'projet_id'              => $request->projet_id,
                'titre_jalon'            => $request->titre_jalon,
                'date_limite_soumission' => $request->date_limite_soumission,
                'statut_client'          => $request->statut_client,
                'fichier_path'           => $path,
                'fichier_nom_original'   => $nomOriginal,
            ]);
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable créé avec succès.');
    }

    /**
     * 3b. STORE NESTED (Project-scoped)
     */
    public function storeForProject(Request $request, Projet $projet)
    {
        $request->validate([
            'titre_jalon'            => 'required|string|max:150',
            'date_limite_soumission' => 'required|date',
            'statut_client'          => 'required|in:en_attente,rejete_avec_corrections,valide',
            'fichier'                => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:20480',
        ]);

        DB::transaction(function () use ($request, $projet) {
            $path = null;
            $nomOriginal = null;

            if ($request->hasFile('fichier')) {
                $file = $request->file('fichier');
                $path = $file->store('livrables', 'private');
                $nomOriginal = $file->getClientOriginalName();
            }

            Livrable::create([
                'projet_id'              => $projet->id,
                'titre_jalon'            => $request->titre_jalon,
                'date_limite_soumission' => $request->date_limite_soumission,
                'statut_client'          => $request->statut_client,
                'fichier_path'           => $path,
                'fichier_nom_original'   => $nomOriginal,
            ]);
        });

        return redirect()->route('projets.show', $projet->id)->with('success', 'Livrable créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $livrable = Livrable::with('projet')->findOrFail($id);
        return view('livrables.show', compact('livrable'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $livrable = Livrable::findOrFail($id);
        $projets = Projet::all();
        return view('livrables.edit', compact('livrable', 'projets'));
    }

    /**
     * 6. UPDATE — remplace le fichier si un nouveau est fourni
     */
    public function update(Request $request, $id)
    {
        $livrable = Livrable::findOrFail($id);

        $request->validate([
            'projet_id'              => 'required|exists:projets,id',
            'titre_jalon'            => 'required|string|max:150',
            'date_limite_soumission' => 'required|date',
            'statut_client'          => 'required|in:en_attente,rejete_avec_corrections,valide',
            'fichier'                => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:20480',
        ]);

        DB::transaction(function () use ($request, $livrable) {
            $path = $livrable->fichier_path;
            $nomOriginal = $livrable->fichier_nom_original;

            if ($request->hasFile('fichier')) {
                // Supprimer l'ancien fichier si présent
                if ($path) {
                    Storage::disk('private')->delete($path);
                }
                $file = $request->file('fichier');
                $path = $file->store('livrables', 'private');
                $nomOriginal = $file->getClientOriginalName();
            }

            $livrable->update([
                'projet_id'              => $request->projet_id,
                'titre_jalon'            => $request->titre_jalon,
                'date_limite_soumission' => $request->date_limite_soumission,
                'statut_client'          => $request->statut_client,
                'fichier_path'           => $path,
                'fichier_nom_original'   => $nomOriginal,
            ]);
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable mis à jour avec succès.');
    }

    /**
     * 7. DESTROY — supprime aussi le fichier physique
     */
    public function destroy($id)
    {
        $livrable = Livrable::findOrFail($id);

        DB::transaction(function () use ($livrable) {
            if ($livrable->fichier_path) {
                Storage::disk('private')->delete($livrable->fichier_path);
            }
            $livrable->delete();
        });

        return redirect()->route('livrables.index')->with('success', 'Livrable supprimé avec succès.');
    }

    /**
     * DOWNLOAD — Téléchargement sécurisé du fichier livrable
     */
    public function download($id)
    {
        $livrable = Livrable::findOrFail($id);

        if (!$livrable->fichier_path || !Storage::disk('private')->exists($livrable->fichier_path)) {
            return back()->with('error', 'Aucun fichier associé à ce livrable.');
        }

        return Storage::disk('private')->download(
            $livrable->fichier_path,
            $livrable->fichier_nom_original ?? 'livrable'
        );
    }
}

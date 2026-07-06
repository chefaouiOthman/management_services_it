<?php

namespace App\Http\Controllers;

use App\Models\SupportCours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupportCoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:support-cours-view', ['only' => ['index', 'show', 'download']]);
        $this->middleware('permission:support-cours-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:support-cours-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:support-cours-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index()
    {
        $supports = SupportCours::with('catalogueFormations')->get();
        return view('supports.index', compact('supports'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('supports.create');
    }

    /**
     * 3. STORE (Modifié pour gérer l'upload sécurisé)
     */
    public function store(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,mp4|max:51200', // max 50MB
        ]);

        DB::transaction(function () use ($request) {
            $file = $request->file('fichier');
            $path = $file->store('supports_cours', 'private');
            $nomOriginal = $file->getClientOriginalName();

            SupportCours::create([
                'nom_fichier'  => $nomOriginal,
                'url_stockage' => $path,
            ]);
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours uploadé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $support = SupportCours::with('catalogueFormations')->findOrFail($id);
        return view('supports.show', compact('support'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $support = SupportCours::findOrFail($id);
        return view('supports.edit', compact('support'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $support = SupportCours::findOrFail($id);

        $request->validate([
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,mp4|max:51200',
        ]);

        DB::transaction(function () use ($request, $support) {
            $path = $support->url_stockage;
            $nomOriginal = $support->nom_fichier;

            if ($request->hasFile('fichier')) {
                if ($path && Storage::disk('private')->exists($path)) {
                    Storage::disk('private')->delete($path);
                }
                $file = $request->file('fichier');
                $path = $file->store('supports_cours', 'private');
                $nomOriginal = $file->getClientOriginalName();
            }

            $support->update([
                'nom_fichier'  => $nomOriginal,
                'url_stockage' => $path,
            ]);
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours mis à jour avec succès.');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $support = SupportCours::findOrFail($id);

        DB::transaction(function () use ($support) {
            if ($support->url_stockage && Storage::disk('private')->exists($support->url_stockage)) {
                Storage::disk('private')->delete($support->url_stockage);
            }
            $support->catalogueFormations()->detach();
            $support->delete();
        });

        return redirect()->route('supports.index')->with('success', 'Support de cours supprimé avec succès.');
    }

    /**
     * DOWNLOAD — Téléchargement sécurisé du support
     */
    public function download($id)
    {
        $support = SupportCours::findOrFail($id);

        if (!$support->url_stockage || !Storage::disk('private')->exists($support->url_stockage)) {
            return back()->with('error', 'Le fichier de ce support n\'existe plus.');
        }

        return Storage::disk('private')->download(
            $support->url_stockage,
            $support->nom_fichier ?? 'support_cours'
        );
    }
}

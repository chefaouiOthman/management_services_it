<?php

namespace App\Http\Controllers;

use App\Models\SessionFormation;
use App\Models\Inscription;
use App\Models\EvaluationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscriptionController extends Controller
{
    /**
     * S'INSCRIRE A UNE SESSION (Accessible à toutes les entités humaines connectées)
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
        ]);

        $userId = auth()->id();

        // Sécurité anti-doublon pour éviter qu'un utilisateur s'inscrive 2 fois à la même session
        $dejaInscrit = Inscription::where('user_id', $userId)
            ->where('session_formation_id', $request->session_formation_id)
            ->exists();

        if ($dejaInscrit) {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cette session.');
        }

        Inscription::create([
            'user_id'              => $userId,
            'session_formation_id' => $request->session_formation_id,
            'date_inscription'     => now(),
            'statut'               => 'confirme',
        ]);

        return redirect()->back()->with('success', 'Votre inscription a bien été enregistrée.');
    }

    /**
     * SOUUMETTRE UNE EVALUATION DE FIN DE SESSION
     */
    public function evaluer(Request $request)
    {
        $request->validate([
            'session_formation_id' => 'required|exists:session_formations,id',
            'note'                 => 'required|integer|between:1,5',
            'commentaire'          => 'nullable|string',
        ]);

        $userId = auth()->id();

        // Sécurité : l'utilisateur doit être inscrit à la session pour pouvoir l'évaluer
        $isInscrit = Inscription::where('user_id', $userId)
            ->where('session_formation_id', $request->session_formation_id)
            ->exists();

        if (!$isInscrit) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas évaluer une session à laquelle vous n\'avez pas participé.');
        }

        EvaluationSession::create([
            'session_formation_id' => $request->session_formation_id,
            'user_id'              => $userId,
            'note'                 => $request->note,
            'commentaire'          => $request->commentaire,
        ]);

        return redirect()->back()->with('success', 'Merci pour votre évaluation !');
    }
}

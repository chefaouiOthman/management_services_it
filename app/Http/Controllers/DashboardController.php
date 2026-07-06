<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today()->toDateString();
        $userId = Auth::id();

        // Pointage du jour de l'utilisateur connecté
        $pointageJour = Pointage::where('user_id', $userId)
            ->where('date_jour', $today)
            ->first();

        // 5 derniers pointages de l'utilisateur
        $derniersPointages = Pointage::where('user_id', $userId)
            ->orderByDesc('date_jour')
            ->orderByDesc('heure_arrivee')
            ->take(5)
            ->get();

        return view('dashboard', compact('pointageJour', 'derniersPointages'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Services\FinanceService;
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
        if (auth()->user()->hasRole('Client')) {
            return redirect()->route('users.show', auth()->id());
        }

        $user = Auth::user();
        $isAdminOrSuperAdmin = $user->hasAnyRole(['Admin', 'Super Admin']);

        if ($isAdminOrSuperAdmin) {
            $pointageJour = null;
            $derniersPointages = collect();
            $tousLesPointagesRecents = Pointage::with('user')
                ->orderByDesc('date_jour')
                ->orderByDesc('heure_arrivee')
                ->take(10)
                ->get();

            $kpis = FinanceService::getKpis();
            $evolution = FinanceService::getEvolutionMensuelle();
            $depenses = FinanceService::getRepartitionDepenses();
            $facturation = FinanceService::getFacturationMensuelle();

            return view('dashboard', compact(
                'pointageJour', 'derniersPointages', 'tousLesPointagesRecents',
                'kpis', 'evolution', 'depenses', 'facturation'
            ));
        }

        $today = Carbon::today()->toDateString();
        $userId = Auth::id();

        $pointageJour = Pointage::where('user_id', $userId)
            ->where('date_jour', $today)
            ->first();

        $derniersPointages = Pointage::where('user_id', $userId)
            ->orderByDesc('date_jour')
            ->orderByDesc('heure_arrivee')
            ->take(5)
            ->get();

        return view('dashboard', compact('pointageJour', 'derniersPointages'));
    }
}

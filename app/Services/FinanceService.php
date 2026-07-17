<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\FichePaie;
use App\Models\NoteDeFrais;
use App\Models\FluxTresorerie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceService
{
    public static function getKpis(): array
    {
        $totalEntrees = FluxTresorerie::where('type_mouvement', 'entree')
            ->sum('montant_operation');

        $totalSorties = FluxTresorerie::where('type_mouvement', 'sortie')
            ->sum('montant_operation');

        return [
            'total_entrees' => round($totalEntrees, 2),
            'total_sorties' => round($totalSorties, 2),
            'solde_net'     => round($totalEntrees - $totalSorties, 2),
        ];
    }

    public static function getEvolutionMensuelle(?int $year = null): array
    {
        $year = $year ?: now()->year;
        $months = [];

        $rows = FluxTresorerie::selectRaw("
                MONTH(date_comptable) as m,
                SUM(CASE WHEN type_mouvement = 'entree' THEN montant_operation ELSE 0 END) as entrees,
                SUM(CASE WHEN type_mouvement = 'sortie' THEN montant_operation ELSE 0 END) as sorties
            ")
            ->whereYear('date_comptable', $year)
            ->groupByRaw('MONTH(date_comptable)')
            ->orderByRaw('MONTH(date_comptable)')
            ->get()
            ->keyBy('m');

        for ($m = 1; $m <= 12; $m++) {
            $label = Carbon::create($year, $m, 1)->locale('fr')->monthName;
            $months[] = [
                'month'   => ucfirst($label),
                'entrees' => round((float)($rows[$m]->entrees ?? 0), 2),
                'sorties' => round((float)($rows[$m]->sorties ?? 0), 2),
            ];
        }

        return $months;
    }

    public static function getRepartitionDepenses(): array
    {
        $masseSalariale = FichePaie::sum('net_a_payer');
        $fraisFonctionnement = NoteDeFrais::sum('montant_ttc');

        return [
            'masse_salariale'     => round($masseSalariale, 2),
            'frais_fonctionnement' => round($fraisFonctionnement, 2),
        ];
    }

    public static function getFacturationMensuelle(?int $year = null): array
    {
        $year = $year ?: now()->year;
        $months = [];

        $rows = Facture::selectRaw("
                MONTH(date_emission) as m,
                SUM(total_calc) as total_facture,
                SUM(CASE WHEN statut_paiement = 'soldee' THEN total_calc ELSE 0 END) as total_encaisse
            ")
            ->join(DB::raw("(
                SELECT
                    lf.facture_id,
                    SUM(lf.quantite * lf.prix_unitaire_ht * (1 + lf.taux_tva / 100)) as total_calc
                FROM ligne_factures lf
                GROUP BY lf.facture_id
            ) as lf_agg"), 'factures.id', '=', 'lf_agg.facture_id')
            ->whereYear('date_emission', $year)
            ->groupByRaw('MONTH(date_emission)')
            ->orderByRaw('MONTH(date_emission)')
            ->get()
            ->keyBy('m');

        for ($m = 1; $m <= 12; $m++) {
            $label = Carbon::create($year, $m, 1)->locale('fr')->monthName;
            $months[] = [
                'month'    => ucfirst($label),
                'facture'  => round((float)($rows[$m]->total_facture ?? 0), 2),
                'encaisse' => round((float)($rows[$m]->total_encaisse ?? 0), 2),
            ];
        }

        return $months;
    }
}

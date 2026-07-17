<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategorieFlux;
use App\Models\FluxTresorerie;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\FichePaie;
use App\Models\NoteDeFrais;
use App\Models\Client;
use App\Models\Employe;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $clients = Client::all();
        $employes = Employe::all();

        // 1. Catégories de Flux
        $catVentes = CategorieFlux::create(['libelle_categorie' => 'Ventes et Prestations', 'code_comptable' => '701000']);
        $catSalaires = CategorieFlux::create(['libelle_categorie' => 'Salaires et Charges', 'code_comptable' => '641000']);
        $catFrais = CategorieFlux::create(['libelle_categorie' => 'Frais de Déplacement', 'code_comptable' => '625000']);

        // 2. Factures (Flux Entrants)
        foreach ($clients as $client) {
            for ($i = 0; $i < 3; $i++) { // 3 factures par client
                $dateEmission = Carbon::now()->subMonths(rand(1, 24));
                $statutPaiement = $faker->randomElement(['emise', 'en_retard_paiement', 'soldee']);
                
                $facture = Facture::create([
                    'client_id' => $client->user_id,
                    'num_facture' => 'FAC-' . $dateEmission->format('Ym') . '-' . $faker->unique()->numberBetween(1000, 9999),
                    'date_emission' => $dateEmission,
                    'statut_paiement' => $statutPaiement,
                ]);

                $totalHT = 0;
                for ($j = 0; $j < rand(1, 4); $j++) {
                    $qte = rand(1, 10);
                    $prix = $faker->randomFloat(2, 100, 2000);
                    LigneFacture::create([
                        'facture_id' => $facture->id,
                        'designation' => 'Prestation ' . $faker->word,
                        'quantite' => $qte,
                        'prix_unitaire_ht' => $prix,
                        'taux_tva' => 20.00,
                    ]);
                    $totalHT += ($qte * $prix);
                }

                $totalTTC = $totalHT * 1.20;

                // Flux de trésorerie pour toute facture (0 NULL)
                $dateComptable = match ($statutPaiement) {
                    'soldee' => Carbon::parse($dateEmission)->addDays(rand(5, 30)),
                    'en_retard_paiement' => Carbon::parse($dateEmission)->addDays(rand(45, 90)),
                    default => Carbon::parse($dateEmission),
                };
                $flux = FluxTresorerie::create([
                    'categorie_flux_id' => $catVentes->id,
                    'type_mouvement' => 'entree',
                    'montant_operation' => $totalTTC,
                    'date_comptable' => $dateComptable,
                ]);

                $facture->update(['flux_tresorerie_id' => $flux->id]);
            }
        }

        // 3. Fiches de Paie (Flux Sortants)
        foreach ($employes as $employe) {
            // Générer 6 fiches de paie pour l'employé
            for ($m = 1; $m <= 6; $m++) {
                $datePaie = Carbon::now()->subMonths($m)->endOfMonth();
                $netAPayer = $faker->randomFloat(2, 4000, 15000);

                // Le flux de trésorerie sortant est créé en même temps (le salaire est payé)
                $flux = FluxTresorerie::create([
                    'categorie_flux_id' => $catSalaires->id,
                    'type_mouvement' => 'sortie',
                    'montant_operation' => $netAPayer,
                    'date_comptable' => $datePaie,
                ]);

                FichePaie::create([
                    'employe_id' => $employe->user_id,
                    'flux_tresorerie_id' => $flux->id, // La FK unidirectionnelle
                    'mois_annee' => $datePaie->format('Y-m'),
                    'net_a_payer' => $netAPayer,
                ]);
            }
        }

        // 4. Notes de Frais (Flux Sortants)
        foreach ($employes as $employe) {
            if ($faker->boolean(50)) { // 50% des employés ont des notes de frais
                for ($n = 0; $n < 2; $n++) {
                    $statutNDF = $faker->randomElement(['soumis', 'approuve_manager', 'rejete', 'rembourse']);
                    $montantNDF = $faker->randomFloat(2, 50, 500);
                    $dateSoumission = Carbon::now()->subMonths(rand(1, 12));

                    $ndf = NoteDeFrais::create([
                        'employe_id' => $employe->user_id,
                        'motif_depense' => 'Déplacement ' . $faker->city,
                        'montant_ttc' => $montantNDF,
                        'justificatif_path' => '/storage/ndf/' . $faker->uuid . '.pdf',
                        'statut_remboursement' => $statutNDF,
                    ]);

                    if (in_array($statutNDF, ['rembourse', 'approuve_manager'])) {
                        $flux = FluxTresorerie::create([
                            'categorie_flux_id' => $catFrais->id,
                            'type_mouvement' => 'sortie',
                            'montant_operation' => $montantNDF,
                            'date_comptable' => $statutNDF === 'rembourse'
                                ? Carbon::parse($dateSoumission)->addDays(rand(10, 20))
                                : Carbon::parse($dateSoumission)->addDays(rand(1, 5)),
                        ]);
                        $ndf->update(['flux_tresorerie_id' => $flux->id]);
                    }
                }
            }
        }
    }
}

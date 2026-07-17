<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogueFormation;
use App\Models\SupportCours;
use App\Models\SessionFormation;
use App\Models\Inscription;
use App\Models\EvaluationSession;
use App\Models\Employe;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $employes = Employe::all();
        // note_technique fixe par employé (indépendante du contexte de la session)
        $noteTechniqueMap = $employes->mapWithKeys(fn($e) => [$e->user_id => rand(3, 5)]);
        // Apprenants potentiels : tout utilisateur (Employé, Stagiaire, Client)
        $apprenants = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Employe_Standard', 'Stagiaire', 'Client']);
        })->get();

        // 1. Supports de cours (Standalone)
        $supports = [];
        for ($i = 1; $i <= 10; $i++) {
            $supports[] = SupportCours::create([
                'nom_fichier' => "Support_Cours_{$i}.pdf",
                'url_stockage' => "https://storage.entreprise.com/formations/support_{$i}.pdf",
            ]);
        }
        $supportCollection = collect($supports);

        // 2. Catalogues de formation
        $catalogues = [];
        $themes = ['Laravel Avancé', 'Sécurité Informatique', 'Management Agile', 'DevOps avec Docker', 'Design Patterns'];
        foreach ($themes as $theme) {
            $catalogue = CatalogueFormation::create([
                'titre_formation' => $theme,
                'description_programme' => $faker->paragraph(3),
                'prix_standard' => $faker->randomFloat(2, 500, 3000),
            ]);
            $catalogues[] = $catalogue;

            // Pivot catalogue_formation_support
            $catalogue->supportCours()->attach(
                $supportCollection->random(rand(1, 3))->pluck('id')->toArray()
            );

            // 3. Sessions de formation pour ce catalogue (2 sessions par catalogue)
            for ($s = 1; $s <= 2; $s++) {
                $dateDebut = Carbon::now()->subMonths(rand(1, 12))->addDays(rand(1, 10));
                $dateFin = $dateDebut->copy()->addDays(rand(2, 5));

                $session = SessionFormation::create([
                    'catalogue_formation_id' => $catalogue->id,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'salle_virtuelle' => $faker->randomElement([null, 'https://zoom.us/j/' . rand(100000000, 999999999)]),
                    'salle_concrete' => $faker->randomElement([null, 'Salle ' . $faker->city]),
                ]);

                // Pivot employe_session_formation (1 ou 2 formateurs par session)
                $formateurs = $employes->random(rand(1, 2));
                $session->formateurs()->attach($formateurs->pluck('user_id')->toArray());

                // 4. Inscriptions (5 à 10 apprenants par session)
                $inscrits = $apprenants->random(rand(5, 10));
                foreach ($inscrits as $inscrit) {
                    $statutInscrit = $faker->randomElement(['valide', 'annule', 'present', 'certifie']);
                    Inscription::create([
                        'user_id' => $inscrit->id,
                        'session_formation_id' => $session->id,
                        'statut_inscription' => $statutInscrit,
                    ]);

                    // 5. Évaluation de la session si l'apprenant était présent ou certifié
                    if (in_array($statutInscrit, ['present', 'certifie'])) {
                        foreach ($formateurs as $formateur) {
                            EvaluationSession::create([
                                'session_formation_id' => $session->id,
                                'user_id' => $inscrit->id,
                                'employe_id' => $formateur->user_id,
                                'note_pedagogie' => rand(3, 5),
                                'note_technique' => $noteTechniqueMap[$formateur->user_id] ?? rand(3, 5),
                                'avis_textuel' => $faker->boolean(70) ? $faker->sentence() : null,
                            ]);
                        }
                    }
                }
            }
        }
    }
}

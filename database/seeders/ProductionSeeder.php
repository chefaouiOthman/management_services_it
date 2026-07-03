<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Employe;
use App\Models\Projet;
use App\Models\Technologie;
use App\Models\Livrable;
use App\Models\Tache;
use App\Models\FeuilleTemps;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        $clients = Client::all();
        $employes = Employe::all();

        // 1. Technologies
        $technologies = [];
        $techNames = ['Laravel', 'Vue.js', 'React', 'Docker', 'MySQL', 'PostgreSQL', 'TailwindCSS'];
        foreach ($techNames as $tech) {
            $technologies[] = Technologie::create([
                'nom_tech' => $tech,
                'version' => $faker->randomElement(['1.0', '2.5', '3.0', '10.0', '8.0']),
            ]);
        }
        $techCollection = collect($technologies);

        // 2. Projets
        $statuts = ['analyse', 'developpement', 'recette', 'deploie', 'maintenance'];
        $projets = [];

        foreach ($clients as $client) {
            // 2 projets par client
            for ($i = 0; $i < 2; $i++) {
                $projet = Projet::create([
                    'client_id' => $client->user_id,
                    'nom_projet' => 'Projet ' . $faker->company,
                    'description' => $faker->paragraph,
                    'budget_vendu' => $faker->randomFloat(2, 5000, 100000),
                    'statut_projet' => $faker->randomElement($statuts),
                    'created_at' => Carbon::now()->subMonths(rand(1, 24)),
                ]);
                $projets[] = $projet;

                // Pivot projet_technologie
                $projet->technologies()->attach(
                    $techCollection->random(rand(2, 4))->pluck('id')->toArray()
                );

                // 3. Livrables pour ce projet
                for ($j = 1; $j <= 3; $j++) {
                    Livrable::create([
                        'projet_id' => $projet->id,
                        'titre_jalon' => "Jalon $j : " . $faker->sentence(3),
                        'date_limite_soumission' => Carbon::parse($projet->created_at)->addMonths($j),
                        'statut_client' => $faker->randomElement(['en_attente', 'rejete_avec_corrections', 'valide']),
                    ]);
                }

                // 4. Tâches pour ce projet (10 par projet)
                for ($k = 1; $k <= 10; $k++) {
                    $tache = Tache::create([
                        'titre_tache' => "Tâche " . $faker->words(3, true),
                    ]);

                    // Pivot projet_tache
                    $projet->taches()->attach($tache->id, [
                        'priorite' => $faker->randomElement(['basse', 'moyenne', 'haute', 'bloquante']),
                        'statut_tache' => $faker->randomElement(['backlog', 'en_cours', 'en_revue', 'termine']),
                    ]);

                    // 5. Feuilles de temps imputées sur cette tâche par 1 à 3 employés
                    $assignedEmployes = $employes->random(rand(1, 3));
                    foreach ($assignedEmployes as $emp) {
                        // 2 feuilles de temps par employé sur cette tâche
                        for ($l = 0; $l < 2; $l++) {
                            $ft = FeuilleTemps::create([
                                'employe_id' => $emp->user_id,
                                'projet_id' => $projet->id,
                                'date_effort' => Carbon::parse($projet->created_at)->addDays(rand(1, 60)),
                                'duree_heures' => $faker->randomFloat(2, 1, 8),
                                'commentaire' => "Travail sur " . $tache->titre_tache,
                            ]);

                            // Pivot feuille_temps_tache
                            $ft->taches()->attach($tache->id);
                        }
                    }
                }
            }
        }
    }
}

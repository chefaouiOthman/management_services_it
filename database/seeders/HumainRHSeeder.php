<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employe;
use App\Models\Stagiaire;
use App\Models\Client;
use App\Models\Departement;
use App\Models\Contrat;
use App\Models\Zone;
use App\Models\Pointage;
use App\Models\HistoriquePassage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class HumainRHSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // 1. Départements
        $nomsDepts = ['Direction Générale', 'Ressources Humaines', 'Développement IT', 'Commercial', 'Support Technique'];
        $depts = [];
        foreach ($nomsDepts as $nom) {
            $depts[] = Departement::create(['nom_departement' => $nom]);
        }

        // 2. Zones (Sécurité)
        $zones = [
            ['code' => 'Z01', 'salle' => 'Accueil', 'niveau' => 1],
            ['code' => 'Z02', 'salle' => 'Open Space', 'niveau' => 2],
            ['code' => 'Z03', 'salle' => 'Salle Serveurs', 'niveau' => 5],
        ];
        $zonesDb = [];
        foreach ($zones as $z) {
            $zonesDb[] = Zone::create([
                'code_zone' => $z['code'],
                'nom_salle' => $z['salle'],
                'niveau_requis' => $z['niveau'],
                'est_active' => true,
            ]);
        }

        // 3. Employés (10 employés)
        $employes = [];
        for ($i = 0; $i < 10; $i++) {
            $dateEmbauche = Carbon::now()->subDays(rand(100, 730));
            $user = User::create([
                'nom_complet' => $faker->name,
                'email' => "employe{$i}@entreprise.com",
                'password' => Hash::make('password123'),
                'est_actif' => true,
            ]);
            $user->assignRole('Employe_Standard');

            $employe = Employe::create([
                'user_id' => $user->id,
                'date_embauche' => $dateEmbauche,
                'CIN' => $faker->unique()->regexify('[A-Z]{2}[0-9]{5,6}'),
            ]);
            $employes[] = $employe;

            // Contrat
            Contrat::create([
                'employe_id' => $employe->user_id,
                'type_contrat' => $faker->randomElement(['CDI', 'CDD']),
                'date_debut' => $dateEmbauche,
                'date_fin' => null,
                'salaire_base' => $faker->randomFloat(2, 4000, 15000),
                'heures_hebdo' => 44,
                'statut' => 'actif',
            ]);
        }

        // 4. Stagiaires (5 stagiaires)
        $stagiaires = [];
        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'nom_complet' => $faker->name,
                'email' => "stagiaire{$i}@entreprise.com",
                'password' => Hash::make('password123'),
                'est_actif' => true,
            ]);
            $user->assignRole('Stagiaire');

            $stagiaires[] = Stagiaire::create([
                'user_id' => $user->id,
                'ecole_origine' => "Ecole " . $faker->company,
                'sujet_stage' => "Sujet de stage de {$user->nom_complet}",
            ]);
        }

        // 5. Clients (5 clients)
        $clients = [];
        for ($i = 0; $i < 5; $i++) {
            $type = $faker->randomElement(['physique', 'morale']);
            $user = User::create([
                'nom_complet' => $faker->name, // Toujours requis par la BDD (même pour une entreprise, c'est le contact)
                'email' => "client{$i}@domaine.com",
                'password' => Hash::make('password123'),
                'est_actif' => true,
            ]);
            $user->assignRole('Client');

            $clients[] = Client::create([
                'user_id' => $user->id,
                'type_client' => $type,
                'nom_societe' => $type === 'morale' ? $faker->company : null,
                'ice' => $type === 'morale' ? $faker->numerify('################') : null,
            ]);
        }

        // 6. Génération de quelques Pointages et Historique de passages pour les employés (dernier mois)
        foreach ($employes as $employe) {
            for ($day = 1; $day <= 30; $day++) {
                if (rand(1, 10) > 8) continue; // 20% de jours d'absence
                
                $dateJour = Carbon::now()->subDays($day);
                // Si c'est un week-end, on ignore
                if ($dateJour->isWeekend()) continue;

                $heureArrivee = $dateJour->copy()->setTime(rand(8, 9), rand(0, 59));
                $heureDepart = $dateJour->copy()->setTime(rand(17, 18), rand(0, 59));
                $statut = $heureArrivee->hour >= 9 ? 'en_retard' : 'a_l_heure';

                Pointage::create([
                    'user_id' => $employe->user_id,
                    'date_jour' => $dateJour->toDateString(),
                    'heure_arrivee' => $heureArrivee,
                    'heure_depart' => $heureDepart,
                    'statut_presence' => $statut,
                ]);

                // Historique de passage à l'entrée
                HistoriquePassage::create([
                    'user_id' => $employe->user_id,
                    'zone_id' => $zonesDb[0]->id, // Accueil
                    'horodatage' => $heureArrivee,
                    'tentative_statut' => 'autorise',
                ]);
            }
        }
    }
}

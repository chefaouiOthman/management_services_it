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
            $cin = $faker->unique()->regexify('[A-Z]{2}[0-9]{5,6}');
            $user = User::create([
                'nom_complet' => $faker->name,
                'email' => "employe{$i}@entreprise.com",
                'password' => Hash::make('password'),
                'est_actif' => true,
                'cin' => $cin,
            ]);
            $user->assignRole('Employe_Standard');

            $employe = Employe::create([
                'user_id' => $user->id,
                'date_embauche' => $dateEmbauche,
                'departement_id' => $depts[array_rand($depts)]->id,
            ]);
            $employes[] = $employe;

            $typeContrat = $faker->randomElement(['CDI', 'CDD', 'Freelance']);
            Contrat::create([
                'employe_id' => $employe->user_id,
                'type_contrat' => $typeContrat,
                'date_debut' => $dateEmbauche,
                'date_fin' => in_array($typeContrat, ['CDD', 'Freelance'])
                    ? (clone $dateEmbauche)->addMonths(rand(6, 12)) : null,
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
                'password' => Hash::make('password'),
                'est_actif' => true,
                'cin' => $faker->unique()->regexify('[A-Z]{2}[0-9]{5,6}'),
            ]);
            $user->assignRole('Stagiaire');

            $stagiaires[] = Stagiaire::create([
                'user_id' => $user->id,
                'ecole_origine' => "Ecole " . $faker->company,
                'sujet_stage' => "Sujet de stage de {$user->nom_complet}",
                'departement_id' => $depts[array_rand($depts)]->id,
            ]);
        }

        // 5. Clients (5 clients)
        $clients = [];
        for ($i = 0; $i < 5; $i++) {
            $type = $faker->randomElement(['physique', 'morale']);
            $user = User::create([
                'nom_complet' => $faker->name,
                'email' => "client{$i}@domaine.com",
                'password' => Hash::make('password'),
                'est_actif' => true,
                'cin' => $faker->unique()->regexify('[A-Z]{2}[0-9]{5,6}'),
            ]);
            $user->assignRole('Client');

            $clients[] = Client::create([
                'user_id' => $user->id,
                'type_client' => $type,
                'nom_societe' => $type === 'morale' ? $faker->company : null,
                'ice' => $type === 'morale' ? $faker->numerify('################') : null,
            ]);
        }

        // 5-bis. 5 Administrateurs supplémentaires (le 6e vient du RolesAndAdminSeeder)
        $additionalAdmins = [];
        for ($i = 2; $i <= 6; $i++) {
            $dateEmbauche = Carbon::now()->subDays(rand(100, 730));
            $user = User::create([
                'nom_complet' => $faker->name,
                'email' => "admin{$i}@entreprise.com",
                'password' => Hash::make('password'),
                'est_actif' => true,
                'cin' => $faker->unique()->regexify('[A-Z]{2}[0-9]{5,6}'),
            ]);
            $user->assignRole('Admin');
            $employe = Employe::create([
                'user_id' => $user->id,
                'date_embauche' => $dateEmbauche,
                'departement_id' => $depts[array_rand($depts)]->id,
            ]);
            Contrat::create([
                'employe_id' => $employe->user_id,
                'type_contrat' => 'CDI',
                'date_debut' => $dateEmbauche,
                'salaire_base' => $faker->randomFloat(2, 12000, 25000),
                'heures_hebdo' => 40,
                'statut' => 'actif',
            ]);
            $additionalAdmins[] = $user;
        }

        // 6. Récupération des IDs Admin pour la logique de répartition
        $adminUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))
            ->orderBy('id')
            ->get();

        $firstAdminId = $adminUsers->first()->id;
        $otherAdminIds = $adminUsers->where('id', '!=', $firstAdminId)->pluck('id')->toArray();

        // 7. Génération des Pointages avec la distribution 50/50 exclusivement Admin
        $allWorkers = array_merge(
            array_map(fn($e) => ['id' => $e->user_id, 'type' => 'employe'], $employes),
            array_map(fn($s) => ['id' => $s->user_id, 'type' => 'stagiaire'], $stagiaires)
        );

        foreach ($allWorkers as $worker) {
            for ($day = 1; $day <= 30; $day++) {
                if (rand(1, 10) > 8) continue;

                $dateJour = Carbon::now()->subDays($day);
                if ($dateJour->isWeekend()) continue;

                $heureArrivee = $dateJour->copy()->setTime(rand(8, 9), rand(0, 59));
                $heureDepart = $dateJour->copy()->setTime(rand(17, 18), rand(0, 59));
                $statut = $heureArrivee->hour >= 9 ? 'en_retard' : 'a_l_heure';

                // Distribution exclusive 50/50 entre Admins uniquement
                $createdBy = rand(0, 1)
                    ? $firstAdminId
                    : $otherAdminIds[array_rand($otherAdminIds)];

                Pointage::create([
                    'user_id'         => $worker['id'],
                    'date_jour'       => $dateJour->toDateString(),
                    'heure_arrivee'   => $heureArrivee,
                    'heure_depart'    => $heureDepart,
                    'statut_presence' => $statut,
                    'created_by'      => $createdBy,
                ]);

                HistoriquePassage::create([
                    'user_id'         => $worker['id'],
                    'zone_id'         => $zonesDb[0]->id,
                    'horodatage'      => $heureArrivee,
                    'tentative_statut' => 'autorise',
                ]);
            }
        }
    }
}

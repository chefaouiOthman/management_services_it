<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeMateriel;
use App\Models\AssetMateriel;
use App\Models\TicketMaintenance;
use App\Models\LicenceLogiciel;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetITSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        // Seuls les employés et stagiaires reçoivent du matériel/licences
        $utilisateurs = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Employe_Standard', 'Stagiaire', 'Admin']);
        })->get();

        // 1. Types de matériel
        $types = [];
        $nomsTypes = ['Ordinateur Portable', 'Écran Externe', 'Clavier Mécanique', 'Souris Ergonomique', 'Casque Audio', 'Tablette'];
        foreach ($nomsTypes as $nom) {
            $types[] = TypeMateriel::create([
                'libelle_type' => $nom,
                'description_type' => "Matériel de type : $nom",
            ]);
        }

        // 2. Matériels (Actifs)
        $materiels = [];
        foreach ($types as $type) {
            // 5 matériels par type
            for ($i = 0; $i < 5; $i++) {
                $statut = $faker->randomElement(['disponible', 'attribue', 'en_panne', 'reforme']);
                $dateAchat = Carbon::now()->subMonths(rand(6, 24));
                $materiel = AssetMateriel::create([
                    'type_materiel_id' => $type->id,
                    'num_serie' => $faker->unique()->bothify('SN-####-????'),
                    'marque' => $faker->randomElement(['Dell', 'HP', 'Lenovo', 'Apple', 'Logitech']),
                    'modele' => 'Modele ' . $faker->word,
                    'date_achat_actif' => $dateAchat,
                    'statut_materiel' => $statut,
                    'prix_achat' => $faker->randomFloat(2, 50, 2500),
                ]);
                $materiels[] = $materiel;

                // 3. Assignation du matériel (si attribué)
                if ($statut === 'attribue' || $statut === 'en_panne') {
                    $user = $utilisateurs->random();
                    $dateRemise = Carbon::parse($dateAchat)->addDays(rand(1, 30));
                    
                    // Pivot assignation_materiels
                    $materiel->users()->attach($user->id, [
                        'date_remise' => $dateRemise,
                        'date_restitution' => null, // Toujours en sa possession
                    ]);
                }

                // Si reformé, il y a un historique d'assignation puis restitution
                if ($statut === 'reforme') {
                    $user = $utilisateurs->random();
                    $dateRemise = Carbon::parse($dateAchat)->addDays(10);
                    $dateRestitution = Carbon::parse($dateRemise)->addMonths(rand(2, 10));

                    $materiel->users()->attach($user->id, [
                        'date_remise' => $dateRemise,
                        'date_restitution' => $dateRestitution,
                    ]);
                }

                // 4. Tickets de maintenance
                if ($statut === 'en_panne' || $statut === 'reforme' || $faker->boolean(20)) {
                    $userSubmitter = $utilisateurs->random();
                    TicketMaintenance::create([
                        'asset_materiel_id' => $materiel->id,
                        'user_id' => $userSubmitter->id,
                        'description_panne' => $faker->sentence(5),
                        'cout_reparation' => $faker->randomFloat(2, 0, 500),
                        'statut_ticket' => $statut === 'en_panne' ? $faker->randomElement(['signale', 'en_atelier']) : 'resolu',
                    ]);
                }
            }
        }

        // 5. Licences Logiciel
        $logiciels = ['Suite Office 365', 'JetBrains IDE', 'Adobe Creative Cloud', 'Antivirus Corporate', 'GitHub Copilot'];
        $licences = [];
        foreach ($logiciels as $nom) {
            for ($i = 0; $i < 3; $i++) {
                $licences[] = LicenceLogiciel::create([
                    'nom_logiciel' => $nom,
                    'cle_licence' => $faker->uuid,
                    'date_expiration' => Carbon::now()->addMonths(rand(1, 24)),
                ]);
            }
        }

        // 6. Assignation des licences
        foreach ($licences as $licence) {
            $assignedUsers = $utilisateurs->random(rand(1, 5));
            foreach ($assignedUsers as $user) {
                // Table d'assignation manuelle (car pas de relation withPivot déclarée explicitement avec le modèle LicenceLogiciel dans GEMINI, bien qu'on ait la table)
                DB::table('assignation_licences')->insert([
                    'user_id' => $user->id,
                    'licence_logiciel_id' => $licence->id,
                    'date_attribution' => Carbon::now()->subMonths(rand(1, 10)),
                    'date_revocation' => $faker->boolean(20) ? Carbon::now()->subDays(rand(1, 20)) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

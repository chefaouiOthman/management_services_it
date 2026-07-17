<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Ordre d'exécution strict pour éviter les erreurs de clés étrangères.
     */
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class, // Modèle Humain (Rôles, Admin)
            PermissionseSeeder::class,  // Super Admin (toutes permissions)
            HumainRHSeeder::class,      // Modèle Humain & RH (Employes, Stagiaires, Clients, Contrats)
            ProductionSeeder::class,    // Modèle Production (Projets, Tâches, etc.)
            FormationSeeder::class,     // Modèle Formation (Catalogues, Sessions, etc.)
            AssetITSeeder::class,       // Modèle Actifs IT (Matériel, Licences)
            FinanceSeeder::class,       // Modèle Finance (Flux liés aux précédentes entités)
        ]);
    }
}

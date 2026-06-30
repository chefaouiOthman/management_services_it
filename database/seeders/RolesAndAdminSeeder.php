<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employe;
use App\Models\Departement;
use App\Models\Contrat;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset du cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Création des rôles
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employe_Standard']);
        Role::firstOrCreate(['name' => 'Stagiaire']);
        Role::firstOrCreate(['name' => 'Client']);
        // 3. Création d'un premier département obligatoire
        $deptDirection = Departement::firstOrCreate([
            'nom_departement' => 'Direction Générale'
        ]);

        // 4. Création du compte utilisateur
        $user = User::create([
            'nom_complet' => 'Othman Chefaooui',
            'email' => 'admin@entreprise.com',
            'password' => Hash::make('password123'),
            'est_actif' => true,
        ]);

        // 5. Création du profil employé associé au département
        $employe = Employe::create([
            'id'             => $user->id,
            'date_embauche'  => now(),
            'CIN'            => 'AB123456',
            'departement_id' => $deptDirection->id,
        ]);

        // 6. Création du contrat initial
        Contrat::create([
            'employe_id'   => $employe->id,
            'type_contrat' => 'CDI',
            'date_debut'   => now(),
            'salaire_base' => 6000.00,
            'heures_hebdo' => 44,
            'statut'       => 'actif',
        ]);

        // 7. Attribution du rôle Admin
        $user->assignRole($roleAdmin);
    }
}

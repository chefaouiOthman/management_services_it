<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employe;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset du cache Spatie pour éviter tout conflit de mémoire
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Création des permissions granulaires du monde réel
        $permissions = [
            // MODULE 1 : HUMAIN
            'view-humain', 'edit-humain', 'delete-humain',

            // MODULE 2 : RH & SÉCURITÉ
            'view-rh', 'edit-rh', 'delete-rh',
            'view-pointages', 'edit-pointages', 'delete-pointages',

            // MODULE 3 : PRODUCTION
            'view-production', 'edit-production', 'delete-production',

            // MODULE 4 : FORMATION
            'view-formation', 'edit-formation', 'delete-formation',
            'view-evaluations', 'edit-evaluations', 'delete-evaluations',

            // MODULE 5 : ACTIFS IT
            'view-assets', 'edit-assets', 'delete-assets',

            // MODULE 6 : FINANCE
            'view-finance', 'edit-finance', 'delete-finance',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 3. Création des rôles (on stocke les variables pour ceux qu'on veut configurer)
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleEmploye = Role::firstOrCreate(['name' => 'Employe_Standard']);
        Role::firstOrCreate(['name' => 'Stagiaire']);
        Role::firstOrCreate(['name' => 'Client']);

        // 4. Attribution des permissions aux rôles
        // --> L'Admin reçoit absolument toutes les permissions de la liste
        $roleAdmin->syncPermissions($permissions);
        
        // --> L'Employé standard reçoit uniquement les droits de lecture
        $roleEmploye->syncPermissions([
            'view-humain', 
            'view-rh', 
            'view-pointages', 
            'view-production', 
            'view-formation', 
            'view-assets',
            'view-finance'
        ]);

        // 5. Création du compte utilisateur (Admin)
        $user = User::firstOrCreate(
            ['email' => 'admin@entreprise.com'],
            [
                'nom_complet' => 'Othman Chefaooui',
                'password' => Hash::make('password123'),
                'est_actif' => true,
            ]
        );

        // 6. Création du profil employé associé (user_id = PK)
        Employe::firstOrCreate(
            ['user_id' => $user->id],
            [
                'date_embauche' => now()->subYears(2),
                'CIN'           => 'AB123456',
            ]
        );

        // 7. Attribution du rôle Admin à l'utilisateur Othman
        $user->assignRole($roleAdmin);
    }
}
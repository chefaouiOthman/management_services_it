<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Employe;
use App\Models\Contrat;
use Illuminate\Support\Facades\Hash;

class PermissionseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. S'assurer que le rôle existe
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // 2. Créer ou récupérer le Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@entreprise.com'],
            [
                'nom_complet' => 'Super Administrateur',
                'password'    => Hash::make('password'),
                'est_actif'   => true,
                'cin'         => 'EE000005',
            ]
        );

        // 3. Forcer la relation dans model_has_roles
        $superAdmin->syncRoles([$superAdminRole]);

        Employe::updateOrCreate(
            ['user_id' => $superAdmin->id],
            ['date_embauche' => now(), 'departement_id' => null]
        );

        Contrat::updateOrCreate(
            ['employe_id' => $superAdmin->id, 'type_contrat' => 'CDI'],
            [
                'date_debut' => now()->subYears(2),
                'salaire_base' => 50000,
                'heures_hebdo' => 40,
                'statut' => 'actif',
            ]
        );
    }
}


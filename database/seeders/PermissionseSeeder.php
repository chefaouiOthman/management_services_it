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

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@entreprise.com'],
            [
                'nom_complet' => 'Super Administrateur',
                'password'    => Hash::make('password'),
                'est_actif'   => true,
                'cin'         => 'EE000005',
            ]
        );
        $superAdminUser->assignRole($superAdmin);
        Employe::firstOrCreate(
            ['user_id' => $superAdminUser->id],
            ['date_embauche' => now(), 'departement_id' => null]
        );
        Contrat::firstOrCreate(
            ['employe_id' => $superAdminUser->id, 'type_contrat' => 'CDI'],
            [
                'date_debut' => now()->subYears(2),
                'salaire_base' => 50000,
                'heures_hebdo' => 40,
                'statut' => 'actif',
            ]
        );
    }
}

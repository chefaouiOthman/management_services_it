<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset du cache Spatie pour éviter tout conflit de mémoire
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Liste des ressources de l'application (31 au total)
        $ressources = [
            // MODULE 1
            'user', 'employe', 'stagiaire', 'client',
            // MODULE 2
            'departement', 'contrat', 'zone', 'historique-passage', 'pointage',
            // MODULE 3
            'projet', 'tache', 'feuille-temps', 'livrable', 'technologie',
            // MODULE 4
            'catalogue-formation', 'session-formation', 'inscription', 'support-cours', 'evaluation',
            // MODULE 5
            'type-materiel', 'asset', 'ticket', 'licence', 'assignation-materiel', 'assignation-licence',
            // MODULE 6
            'categorie-flux', 'flux-tresorerie', 'facture', 'ligne-facture', 'fiche-paie', 'note-de-frais'
        ];

        // Génération des 4 permissions par ressource (Total : 124 permissions)
        $allPermissions = [];
        foreach ($ressources as $ressource) {
            $allPermissions[] = $ressource . '-view';
            $allPermissions[] = $ressource . '-create';
            $allPermissions[] = $ressource . '-edit';
            $allPermissions[] = $ressource . '-delete';
        }

        // Création en base de données
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 3. Création des rôles
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleEmploye = Role::firstOrCreate(['name' => 'Employe_Standard']);
        $roleStagiaire = Role::firstOrCreate(['name' => 'Stagiaire']);
        $roleClient = Role::firstOrCreate(['name' => 'Client']);

        // 4. Attribution des permissions aux rôles
        // --> L'Admin reçoit absolument toutes les permissions (124 permissions)
        $roleAdmin->syncPermissions($allPermissions);
        
        // --> L'Employé standard
        // Lecture sur ce qui est public ou utile
        $employePermissions = [
            'employe-view', 'departement-view', 'projet-view', 'tache-view',
            'catalogue-formation-view', 'session-formation-view',
            'type-materiel-view', 'asset-view'
        ];
        // Self-Service (Création/Édition sur ses propres éléments)
        $selfService = [
            'pointage-view', 'pointage-create', 'pointage-edit',
            'feuille-temps-view', 'feuille-temps-create', 'feuille-temps-edit',
            'evaluation-view', 'evaluation-create', 'evaluation-edit',
            'ticket-view', 'ticket-create', 'ticket-edit',
            'note-de-frais-view', 'note-de-frais-create', 'note-de-frais-edit',
            'inscription-view', 'inscription-create' // Peut s'inscrire
        ];
        $roleEmploye->syncPermissions(array_merge($employePermissions, $selfService));

        // --> Stagiaire (Droits encore plus réduits)
        $stagiairePermissions = [
            'stagiaire-view', 'departement-view', 'projet-view', 'tache-view',
            'catalogue-formation-view', 'session-formation-view',
            'pointage-view', 'pointage-create', 'pointage-edit',
            'feuille-temps-view', 'feuille-temps-create', 'feuille-temps-edit',
            'evaluation-view', 'evaluation-create', 'evaluation-edit',
            'ticket-view', 'ticket-create', 'ticket-edit'
        ];
        $roleStagiaire->syncPermissions($stagiairePermissions);

        // --> Client (Lecture sur ses projets, factures, livrables)
        $clientPermissions = [
            'projet-view', 'livrable-view', 'livrable-edit', // Peut commenter/valider un livrable
            'facture-view', 'ligne-facture-view', 'ticket-view', 'ticket-create'
        ];
        $roleClient->syncPermissions($clientPermissions);

        // 5. Création du compte utilisateur (Admin)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@entreprise.com'],
            [
                'nom_complet' => 'Administrateur Système',
                'password'    => bcrypt('password'), // password fixe pour dev
                'est_actif'   => true,
            ]
        );
        $adminUser->assignRole($roleAdmin);

        // Création d'utilisateurs de test supplémentaires pour que la table ne soit pas vide 
        // et pour pouvoir tester les autres rôles.
        $employeUser = User::firstOrCreate(
            ['email' => 'employe@entreprise.com'],
            [
                'nom_complet' => 'Employé Test',
                'password'    => bcrypt('password'),
                'est_actif'   => true,
            ]
        );
        $employeUser->assignRole($roleEmploye);

        $stagiaireUser = User::firstOrCreate(
            ['email' => 'stagiaire@entreprise.com'],
            [
                'nom_complet' => 'Stagiaire Test',
                'password'    => bcrypt('password'),
                'est_actif'   => true,
            ]
        );
        $stagiaireUser->assignRole($roleStagiaire);

        $clientUser = User::firstOrCreate(
            ['email' => 'client@entreprise.com'],
            [
                'nom_complet' => 'Client Test',
                'password'    => bcrypt('password'),
                'est_actif'   => true,
            ]
        );
        $clientUser->assignRole($roleClient);
    }
}

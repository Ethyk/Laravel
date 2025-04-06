<?php

// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

// class RolesAndPermissionsSeeder extends Seeder
// {
//     public function run()
//     {
//         // Création des rôles
//         $adminRole = Role::create(['name' => 'admin']);
//         $gestionnaireRole = Role::create(['name' => 'gestionnaire']);
//         $tatoueurRole = Role::create(['name' => 'tatoueur']);
//         $clientRole = Role::create(['name' => 'client']);

//         // Création des permissions générales
//         $viewDashboardPermission = Permission::create(['name' => 'view dashboard']);
//         $manageUsersPermission = Permission::create(['name' => 'manage users']);
//         $createSalonPermission = Permission::create(['name' => 'create salon']);
//         $manageSalonPermission = Permission::create(['name' => 'manage salon']);
//         $viewSalonPermission = Permission::create(['name' => 'view salon']);
        
//         // Permissions spécifiques aux portfolios
//         $createPortfolioPermission = Permission::create(['name' => 'create portfolio']);
//         $editPortfolioPermission = Permission::create(['name' => 'edit portfolio']);
//         $viewPortfolioPermission = Permission::create(['name' => 'view portfolio']);
//         $deletePortfolioPermission = Permission::create(['name' => 'delete portfolio']);

//         // Permissions spécifiques aux flashs
//         $createFlashPermission = Permission::create(['name' => 'create flash']);
//         $manageFlashPermission = Permission::create(['name' => 'manage flash']);
//         $viewFlashPermission = Permission::create(['name' => 'view flash']);

//         // Permissions pour les demandes de clients
//         $createDemandePermission = Permission::create(['name' => 'create demande']);
//         $viewDemandePermission = Permission::create(['name' => 'view demande']);
//         $manageDemandePermission = Permission::create(['name' => 'manage demande']);

//         // Assignation des permissions aux rôles

//         // Admin - Toutes les permissions
//         $adminRole->givePermissionTo(Permission::all());

//         // Gestionnaire - Gestion des salons
//         $gestionnaireRole->givePermissionTo([
//             $createSalonPermission,
//             $manageSalonPermission,
//             $viewSalonPermission,
//             $viewDashboardPermission
//         ]);

//         // Tatoueur - Gestion des flashs, portfolios et consultations
//         $tatoueurRole->givePermissionTo([
//             $createFlashPermission,
//             $manageFlashPermission,
//             $viewFlashPermission,
//             $createPortfolioPermission,
//             $editPortfolioPermission,
//             $viewPortfolioPermission,
//             $deletePortfolioPermission
//         ]);

//         // Client - Actions de base comme visualiser
//         $clientRole->givePermissionTo([
//             $viewSalonPermission,
//             $viewFlashPermission,
//             $createDemandePermission,
//             $viewDemandePermission,
//             $viewPortfolioPermission
//         ]);

//         // Confirmation dans la console
//         $this->command->info('Tous les rôles et permissions ont été créés avec succès.');
//     }
// }

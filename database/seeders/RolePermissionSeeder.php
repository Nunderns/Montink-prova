<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            'manage-products',
            'manage-orders',
            'manage-users',
            'view-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar funções e atribuir permissões
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleManager = Role::create(['name' => 'gerente']);
        $roleUser = Role::create(['name' => 'usuario']);

        // Admin tem todas as permissões
        $roleAdmin->givePermissionTo(Permission::all());
        
        // Gerente pode gerenciar produtos e pedidos
        $roleManager->givePermissionTo(['manage-products', 'manage-orders', 'view-reports']);

        // Criar um usuário admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');

        // Criar um usuário gerente
        $manager = User::create([
            'name' => 'Gerente',
            'email' => 'gerente@example.com',
            'password' => bcrypt('password'),
        ]);

        $manager->assignRole('gerente');

        // Criar um usuário comum
        $user = User::create([
            'name' => 'Usuário',
            'email' => 'usuario@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('usuario');
    }
}

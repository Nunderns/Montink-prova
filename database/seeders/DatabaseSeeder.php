<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar o seeder de funções e permissões
        $this->call([
            RolePermissionSeeder::class,
        ]);
        
        // Criar usuários de teste adicionais
        User::factory(10)->create();
    }
}

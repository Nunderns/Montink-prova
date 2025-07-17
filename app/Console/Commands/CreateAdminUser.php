<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um novo usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password') ?? 'password123';

        // Verificar se o usuário já existe
        if (User::where('email', $email)->exists()) {
            $user = User::where('email', $email)->first();
            $this->info("Usuário com o email {$email} já existe. Atualizando para administrador...");
        } else {
            // Criar novo usuário
            $user = User::create([
                'name' => 'Administrador',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info("Novo usuário administrador criado com sucesso!");
        }

        // Atribuir papel de administrador
        $user->assignRole('admin');

        $this->line("\nCredenciais de acesso:");
        $this->line("Email: " . $email);
        $this->line("Senha: " . $password);
        $this->line("\nURL de login: http://localhost:8000/login");
        $this->line("URL do painel: http://localhost:8000/admin/coupons");
    }
}

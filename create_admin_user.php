<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware_Console_Kernel::class);

$app->boot();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Criar usuário administrador
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin_user@example.com',
        'password' => Hash::make('admin123'),
        'email_verified_at' => now(),
    ]);

    // Atribuir papel de administrador
    $user->assignRole('admin');

    echo "Usuário administrador criado com sucesso!\n";
    echo "Email: admin_user@example.com\n";
    echo "Senha: admin123\n";
} catch (\Exception $e) {
    echo "Erro ao criar usuário: " . $e->getMessage() . "\n";
}

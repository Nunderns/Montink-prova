<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

try {
    // Criar a role admin se não existir
    Role::firstOrCreate(['name' => 'admin']);
    
    // Criar ou atualizar o usuário admin
    $user = User::updateOrCreate(
        ['email' => 'admin@montink.com'],
        [
            'name' => 'Administrador',
            'password' => Hash::make('Montink@2025'),
            'email_verified_at' => now(),
        ]
    );

    // Atribuir papel de administrador
    $user->assignRole('admin');

    echo "\n========================================\n";
    echo "Usuário administrador criado/atualizado com sucesso!\n";
    echo "Email: admin@montink.com\n";
    echo "Senha: Montink@2025\n";
    echo "========================================\n\n";
} catch (\Exception $e) {
    echo "\nErro ao criar usuário: " . $e->getMessage() . "\n\n";
    if (str_contains($e->getMessage(), 'SQLSTATE')) {
        echo "Verifique se o banco de dados está configurado corretamente.\n";
        echo "Execute as migrações com: php artisan migrate\n";
    }
}

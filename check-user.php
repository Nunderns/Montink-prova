<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Ill\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Listar todos os usuários
$users = User::all();

echo "Usuários cadastrados:\n\n";

foreach ($users as $user) {
    echo "ID: " . $user->id . "\n";
    echo "Nome: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Admin: " . ($user->hasRole('admin') ? 'Sim' : 'Não') . "\n";
    echo "Criado em: " . $user->created_at . "\n";
    echo "----------------------------------------\n";
}

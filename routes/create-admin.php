<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Criar usuário administrador
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'email_verified_at' => now(),
]);

// Atribuir papel de administrador
$user->assignRole('admin');

echo "Usuário administrador criado com sucesso!\n";
echo "Email: admin@example.com\n";
echo "Senha: password123\n";

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar campos ausentes na tabela pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('frete', 10, 2)->default(0)->after('valor_final');
            $table->string('forma_pagamento', 50)->nullable()->after('frete');
            // $table->foreignId('user_address_id')->nullable()->after('cliente_id')->constrained('user_addresses')->onDelete('set null'); // Removido porque já existe
            
            // Atualizar o enum de status para usar os mesmos valores do modelo
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->change();
        });
        
        // Remover a tabela pedido_itens antiga se existir
        Schema::dropIfExists('pedido_itens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['frete', 'forma_pagamento']); // Removido user_address_id do drop, pois já existe em outra migration
            $table->enum('status', ['pendente', 'pago', 'em_processamento', 'enviado', 'entregue', 'cancelado'])->default('pendente')->change();
        });
    }
};

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
        // Verificar se a tabela já existe
        if (!Schema::hasTable('pedido_items')) {
            // Criar a tabela apenas se não existir
            Schema::create('pedido_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
                $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
                $table->foreignId('variacao_id')->nullable()->constrained('estoque')->onDelete('set null');
                $table->integer('quantidade');
                $table->decimal('preco_unitario', 10, 2);
                $table->decimal('total', 10, 2);
                $table->timestamps();
                
                // Índices para melhorar o desempenho das consultas
                $table->index('pedido_id');
                $table->index('produto_id');
                $table->index('variacao_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pedido_items')) {
            Schema::dropIfExists('pedido_items');
        }
    }
};

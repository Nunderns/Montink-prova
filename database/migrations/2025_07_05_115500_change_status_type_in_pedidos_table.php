<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Alterar a coluna status para string
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pendente'");
        
        // Adicionar constraint de verificação para garantir valores válidos
        DB::statement("
            ALTER TABLE pedidos 
            ADD CONSTRAINT chk_status 
            CHECK (status IN ('pendente', 'pago', 'em_processamento', 'enviado', 'entregue', 'cancelado'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Remover a constraint de verificação
        DB::statement("ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS chk_status");
        
        // Voltar para o tipo enum original
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('pendente', 'pago', 'em_processamento', 'enviado', 'entregue', 'cancelado') NOT NULL DEFAULT 'pendente'");
    }
};

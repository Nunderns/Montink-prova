<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pedido_itens') && !Schema::hasTable('pedido_items')) {
            Schema::rename('pedido_itens', 'pedido_items');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pedido_items') && !Schema::hasTable('pedido_itens')) {
            Schema::rename('pedido_items', 'pedido_itens');
        }
    }
};

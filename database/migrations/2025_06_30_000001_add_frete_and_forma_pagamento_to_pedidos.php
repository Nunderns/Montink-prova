<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'frete')) {
                $table->decimal('frete', 10, 2)->default(0)->after('valor_final');
            }
            if (!Schema::hasColumn('pedidos', 'forma_pagamento')) {
                $table->string('forma_pagamento', 50)->nullable()->after('frete');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'forma_pagamento')) {
                $table->dropColumn('forma_pagamento');
            }
            if (Schema::hasColumn('pedidos', 'frete')) {
                $table->dropColumn('frete');
            }
        });
    }
};

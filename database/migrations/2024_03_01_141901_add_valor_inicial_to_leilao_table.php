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
        Schema::table('leilao', function (Blueprint $table) {
            $table->decimal('valor_inicial', 10, 2)->after('descricao');
            $table->dateTime('data_inicio')->default(now())->after('valor_inicial');
            $table->dateTime('data_termino')->default(now())->after('data_inicio');
            $table->enum('status', ['ABERTO', 'FINALIZADO', 'EXPIRADOS', 'INATIVOS'])->after('data_termino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leilao', function (Blueprint $table) {
            //
        });
    }
};

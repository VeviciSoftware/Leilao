<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leilao', function (Blueprint $table) {
            // Remove a coluna antiga
            $table->dropColumn('status');
        });

        Schema::table('leilao', function (Blueprint $table) {
            // Adiciona a nova coluna com o enum desejado
            $table->enum('status', ['ABERTO', 'FINALIZADO', 'EXPIRADO', 'INATIVO'])->default('INATIVO');
        });
    }

    public function down()
    {
        Schema::table('leilao', function (Blueprint $table) {
            // Remove a coluna criada na migração up
            $table->dropColumn('status');
        });

        Schema::table('leilao', function (Blueprint $table) {
            // Adiciona a coluna antiga com o enum original
            $table->enum('status', ['ABERTO', 'FINALIZADO', 'EXPIRADO'])->default('ABERTO');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leilao', function (Blueprint $table) {
            $table->enum('status', ['ABERTO', 'FINALIZADO', 'EXPIRADO', 'INATIVO'])->change();
        });
    }

    public function down()
    {
        Schema::table('leilao', function (Blueprint $table) {
            $table->enum('status', ['ABERTO', 'FINALIZADO', 'EXPIRADO'])->change();
        });
    }
};

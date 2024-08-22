<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeilaoGanhadorToLeilaoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leilao', function (Blueprint $table) {
            $table->unsignedBigInteger('leilao_ganhador')->nullable()->after('status');
            $table->foreign('leilao_ganhador')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leilao', function (Blueprint $table) {
            $table->dropForeign(['leilao_ganhador']);
            $table->dropColumn('leilao_ganhador');
        });
    }
}
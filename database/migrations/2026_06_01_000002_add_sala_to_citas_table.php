<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->enum('estado_sala', ['sin_llegar', 'esperando', 'en_atencion', 'atendido'])
                ->default('sin_llegar')->after('sala_video');
            $table->timestamp('hora_llegada')->nullable()->after('estado_sala');
            $table->timestamp('hora_atencion')->nullable()->after('hora_llegada');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['estado_sala', 'hora_llegada', 'hora_atencion']);
        });
    }
};

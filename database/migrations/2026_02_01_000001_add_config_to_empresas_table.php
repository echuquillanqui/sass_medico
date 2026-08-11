<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('color_primario')->default('#7c3aed')->after('logo');
            $table->string('moneda', 5)->default('S/')->after('color_primario');
            $table->string('horario_inicio')->nullable()->after('moneda');
            $table->string('horario_fin')->nullable()->after('horario_inicio');
            $table->string('dias_atencion')->nullable()->after('horario_fin');
            $table->string('sitio_web')->nullable()->after('dias_atencion');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['color_primario', 'moneda', 'horario_inicio', 'horario_fin', 'dias_atencion', 'sitio_web']);
        });
    }
};

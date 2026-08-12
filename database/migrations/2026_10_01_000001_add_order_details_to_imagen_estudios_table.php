<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imagen_estudios', function (Blueprint $table) {
            $table->string('tipo_estudio', 160)->nullable()->after('region');
            $table->string('orden_archivo')->nullable()->after('archivo_nombre');
            $table->string('orden_nombre')->nullable()->after('orden_archivo');
        });
    }

    public function down(): void
    {
        Schema::table('imagen_estudios', function (Blueprint $table) {
            $table->dropColumn(['tipo_estudio', 'orden_archivo', 'orden_nombre']);
        });
    }
};

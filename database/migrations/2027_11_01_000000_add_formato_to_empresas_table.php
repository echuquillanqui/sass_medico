<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (! Schema::hasColumn('empresas', 'separador_decimal')) {
                $table->string('separador_decimal', 1)->default('.')->after('moneda');
            }
            if (! Schema::hasColumn('empresas', 'separador_miles')) {
                $table->string('separador_miles', 1)->default(',')->after('separador_decimal');
            }
            if (! Schema::hasColumn('empresas', 'decimales')) {
                $table->unsignedTinyInteger('decimales')->default(2)->after('separador_miles');
            }
            if (! Schema::hasColumn('empresas', 'moneda_posicion')) {
                $table->string('moneda_posicion', 10)->default('antes')->after('decimales'); // antes | despues
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['separador_decimal', 'separador_miles', 'decimales', 'moneda_posicion']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tipo de afectación del IGV (catálogo 07 SUNAT): 10=Gravado, 20=Exonerado,
 * 30=Inafecto. Muchos servicios de salud están exonerados, por lo que el
 * comprobante debe poder emitirse sin IGV.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('facturacion_configs', 'afectacion_igv')) {
                $table->string('afectacion_igv', 2)->default('10')->after('igv_porcentaje');
            }
        });

        Schema::table('comprobantes', function (Blueprint $table) {
            if (! Schema::hasColumn('comprobantes', 'afectacion')) {
                $table->string('afectacion', 2)->default('10')->after('moneda');
            }
            if (! Schema::hasColumn('comprobantes', 'exonerado')) {
                $table->decimal('exonerado', 10, 2)->default(0)->after('gravado');
            }
            if (! Schema::hasColumn('comprobantes', 'inafecto')) {
                $table->decimal('inafecto', 10, 2)->default(0)->after('exonerado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facturacion_configs', function (Blueprint $table) {
            if (Schema::hasColumn('facturacion_configs', 'afectacion_igv')) {
                $table->dropColumn('afectacion_igv');
            }
        });

        Schema::table('comprobantes', function (Blueprint $table) {
            foreach (['afectacion', 'exonerado', 'inafecto'] as $col) {
                if (Schema::hasColumn('comprobantes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

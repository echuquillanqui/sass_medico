<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - comprobantes.baja_pendiente: la comunicación de baja de una factura se envió
 *   a SUNAT y espera confirmación por ticket (no se anula hasta confirmarla).
 * - facturacion_configs: serie/correlativo de nota independientes para boletas,
 *   porque la nota de una boleta debe usar serie con prefijo "B" y la de una
 *   factura, prefijo "F".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            if (! Schema::hasColumn('comprobantes', 'baja_pendiente')) {
                $table->boolean('baja_pendiente')->default(false)->after('baja_via_resumen');
            }
        });

        Schema::table('facturacion_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('facturacion_configs', 'serie_nota_boleta')) {
                $table->string('serie_nota_boleta', 4)->default('BC01')->after('serie_nota');
            }
            if (! Schema::hasColumn('facturacion_configs', 'correlativo_nota_boleta')) {
                $table->unsignedInteger('correlativo_nota_boleta')->default(0)->after('correlativo_nota');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            if (Schema::hasColumn('comprobantes', 'baja_pendiente')) {
                $table->dropColumn('baja_pendiente');
            }
        });

        Schema::table('facturacion_configs', function (Blueprint $table) {
            foreach (['serie_nota_boleta', 'correlativo_nota_boleta'] as $col) {
                if (Schema::hasColumn('facturacion_configs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

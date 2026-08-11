<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Resumen Diario de Boletas (RC) para reportar boletas y sus notas a SUNAT en
 * lote. Guarda la cabecera del resumen; el detalle son los comprobantes que
 * apunten a él mediante comprobantes.resumen_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('resumenes')) {
            Schema::create('resumenes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
                $table->date('fecha_generacion');            // fecha de emisión de las boletas incluidas
                $table->date('fecha_resumen');               // fecha de generación/envío del resumen
                $table->unsignedInteger('correlativo');      // correlativo diario del RC
                $table->string('identificador', 30);         // RC-YYYYMMDD-N
                $table->enum('estado', ['pendiente', 'enviado', 'aceptado', 'rechazado'])->default('pendiente');
                $table->string('sunat_ticket')->nullable();  // ticket asíncrono devuelto por SUNAT
                $table->text('sunat_respuesta')->nullable();
                $table->string('xml_path')->nullable();
                $table->unsignedInteger('total_documentos')->default(0);
                $table->decimal('total_importe', 12, 2)->default(0);
                $table->timestamps();
                $table->unique(['empresa_id', 'fecha_resumen', 'correlativo']);
                $table->index(['empresa_id', 'estado']);
            });
        }

        Schema::table('comprobantes', function (Blueprint $table) {
            if (! Schema::hasColumn('comprobantes', 'resumen_id')) {
                // Resumen que da de ALTA (adiciona) la boleta ante SUNAT.
                $table->foreignId('resumen_id')->nullable()->after('pago_id')->constrained('resumenes')->nullOnDelete();
            }
            if (! Schema::hasColumn('comprobantes', 'resumen_baja_id')) {
                // Resumen que comunica la BAJA de una boleta ya reportada.
                $table->foreignId('resumen_baja_id')->nullable()->after('resumen_id')->constrained('resumenes')->nullOnDelete();
            }
            if (! Schema::hasColumn('comprobantes', 'baja_via_resumen')) {
                // Boleta ya reportada que fue anulada y debe informarse como baja en un próximo resumen.
                $table->boolean('baja_via_resumen')->default(false)->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            if (Schema::hasColumn('comprobantes', 'resumen_baja_id')) {
                $table->dropConstrainedForeignId('resumen_baja_id');
            }
            if (Schema::hasColumn('comprobantes', 'resumen_id')) {
                $table->dropConstrainedForeignId('resumen_id');
            }
            if (Schema::hasColumn('comprobantes', 'baja_via_resumen')) {
                $table->dropColumn('baja_via_resumen');
            }
        });

        Schema::dropIfExists('resumenes');
    }
};

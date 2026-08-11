<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('facturacion_configs', 'serie_nota')) {
                $table->string('serie_nota', 4)->default('FC01')->after('serie_factura');
            }
            if (! Schema::hasColumn('facturacion_configs', 'correlativo_nota')) {
                $table->unsignedInteger('correlativo_nota')->default(0)->after('correlativo_factura');
            }
        });

        Schema::table('comprobantes', function (Blueprint $table) {
            if (! Schema::hasColumn('comprobantes', 'ref_comprobante_id')) {
                $table->foreignId('ref_comprobante_id')->nullable()->after('tipo')->constrained('comprobantes')->nullOnDelete();
            }
            if (! Schema::hasColumn('comprobantes', 'tipo_nota')) {
                $table->string('tipo_nota', 2)->nullable()->after('ref_comprobante_id'); // catálogo 09 SUNAT
            }
            if (! Schema::hasColumn('comprobantes', 'motivo')) {
                $table->string('motivo')->nullable()->after('tipo_nota');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facturacion_configs', function (Blueprint $table) {
            $table->dropColumn(['serie_nota', 'correlativo_nota']);
        });
        Schema::table('comprobantes', function (Blueprint $table) {
            if (Schema::hasColumn('comprobantes', 'ref_comprobante_id')) {
                $table->dropConstrainedForeignId('ref_comprobante_id');
            }
            $table->dropColumn(['tipo_nota', 'motivo']);
        });
    }
};

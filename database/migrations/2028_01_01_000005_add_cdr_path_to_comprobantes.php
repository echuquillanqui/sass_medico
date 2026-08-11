<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda la ruta del CDR (Constancia de Recepción · ZIP de SUNAT) que se debe
 * conservar como respaldo de la aceptación del comprobante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            if (! Schema::hasColumn('comprobantes', 'cdr_path')) {
                $table->string('cdr_path')->nullable()->after('xml_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            if (Schema::hasColumn('comprobantes', 'cdr_path')) {
                $table->dropColumn('cdr_path');
            }
        });
    }
};

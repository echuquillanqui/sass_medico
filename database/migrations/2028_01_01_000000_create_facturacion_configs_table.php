<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('facturacion_configs')) {
            return;
        }

        Schema::create('facturacion_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            // Estado y modo
            $table->boolean('habilitada')->default(false);
            $table->boolean('emitir_automatico')->default(true);
            $table->enum('driver', ['ninguno', 'greenter'])->default('ninguno');
            $table->enum('entorno', ['beta', 'produccion'])->default('beta');
            // Datos del emisor
            $table->string('ruc', 11)->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion_fiscal')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            // Credenciales SUNAT
            $table->string('sol_usuario')->nullable();
            $table->text('sol_clave')->nullable();          // cifrada
            $table->string('certificado_ruta')->nullable(); // .pem
            // Series y correlativos
            $table->string('serie_boleta', 4)->default('B001');
            $table->string('serie_factura', 4)->default('F001');
            $table->unsignedInteger('correlativo_boleta')->default(0);
            $table->unsignedInteger('correlativo_factura')->default(0);
            $table->decimal('igv_porcentaje', 5, 2)->default(18);
            $table->string('moneda', 3)->default('PEN');
            $table->timestamps();
            $table->unique('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_configs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comprobantes')) {
            return;
        }

        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->enum('tipo', ['boleta', 'factura', 'nota_credito'])->default('boleta');
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');
            // Cliente / receptor
            $table->string('cliente_tipo_doc', 1)->default('1'); // 1=DNI, 6=RUC, 0=sin doc
            $table->string('cliente_num_doc', 15)->nullable();
            $table->string('cliente_nombre')->nullable();
            // Importes
            $table->string('moneda', 3)->default('PEN');
            $table->decimal('gravado', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->json('items')->nullable();
            // Estado ante SUNAT
            $table->enum('estado', ['pendiente', 'emitido', 'aceptado', 'rechazado', 'anulado'])->default('pendiente');
            $table->string('sunat_ticket')->nullable();
            $table->text('sunat_respuesta')->nullable();
            $table->string('hash')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->date('fecha_emision');
            $table->timestamps();
            $table->index(['empresa_id', 'tipo', 'estado']);
            $table->unique(['empresa_id', 'serie', 'correlativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};

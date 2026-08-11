<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_oftalmo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            // Ojo derecho (OD)
            $table->string('od_av')->nullable();          // agudeza visual p.ej. 20/20
            $table->decimal('od_esfera', 4, 2)->nullable();
            $table->decimal('od_cilindro', 4, 2)->nullable();
            $table->unsignedSmallInteger('od_eje')->nullable();
            $table->decimal('od_pio', 4, 1)->nullable();  // presión intraocular mmHg
            // Ojo izquierdo (OS)
            $table->string('os_av')->nullable();
            $table->decimal('os_esfera', 4, 2)->nullable();
            $table->decimal('os_cilindro', 4, 2)->nullable();
            $table->unsignedSmallInteger('os_eje')->nullable();
            $table->decimal('os_pio', 4, 1)->nullable();
            $table->string('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_oftalmo');
    }
};

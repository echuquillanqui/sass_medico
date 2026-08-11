<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('cama_id')->nullable()->constrained('camas')->nullOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('especialidad_id')->nullable()->constrained('especialidades')->nullOnDelete();
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_alta')->nullable();
            $table->enum('estado', ['activa', 'alta'])->default('activa');
            $table->text('motivo_ingreso')->nullable();
            $table->text('diagnostico_ingreso')->nullable();
            $table->text('resumen_alta')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizaciones');
    }
};

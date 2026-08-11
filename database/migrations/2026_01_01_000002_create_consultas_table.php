<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('especialidad_id')->nullable()->constrained('especialidades')->nullOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->date('fecha');
            $table->text('motivo')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->string('presion_arterial')->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->json('datos_especialidad')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};

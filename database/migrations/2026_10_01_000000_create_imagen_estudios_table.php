<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagen_estudios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();     // solicitante
            $table->foreignId('radiologo_id')->nullable()->constrained('users')->nullOnDelete();  // informa
            $table->string('modalidad');            // Radiografia, Ecografia, TAC, RM, Mamografia...
            $table->string('region')->nullable();   // Torax, Abdomen, Rodilla...
            $table->date('fecha');
            $table->enum('estado', ['solicitado', 'realizado', 'informado'])->default('solicitado');
            $table->text('indicacion')->nullable(); // motivo del estudio
            $table->text('hallazgos')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('archivo')->nullable();  // imagen/PDF en storage/public
            $table->string('archivo_nombre')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagen_estudios');
    }
};

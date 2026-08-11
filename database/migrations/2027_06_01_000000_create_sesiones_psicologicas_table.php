<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_psicologicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->unsignedSmallInteger('numero')->nullable();     // n.º de sesión
            $table->string('motivo')->nullable();
            $table->string('enfoque')->nullable();                  // TCC, psicoanálisis, sistémico…
            $table->text('desarrollo')->nullable();                 // notas de la sesión
            $table->text('tareas')->nullable();                     // tareas / plan
            $table->unsignedTinyInteger('estado_animo')->nullable(); // 1-10
            $table->unsignedTinyInteger('progreso')->nullable();     // 0-100 %
            $table->date('proxima_cita')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_psicologicas');
    }
};

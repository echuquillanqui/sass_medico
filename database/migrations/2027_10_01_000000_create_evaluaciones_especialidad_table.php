<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('evaluaciones_especialidad')) {
            return;
        }

        Schema::create('evaluaciones_especialidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('especialidad_slug', 60);
            $table->date('fecha');
            $table->json('datos')->nullable();      // { campo: valor } según la config de la especialidad
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'especialidad_slug', 'paciente_id'], 'eval_esp_emp_slug_pac_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_especialidad');
    }
};

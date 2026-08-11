<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_cardio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->unsignedSmallInteger('pa_sistolica')->nullable();
            $table->unsignedSmallInteger('pa_diastolica')->nullable();
            $table->unsignedSmallInteger('fc')->nullable();
            $table->unsignedSmallInteger('colesterol_total')->nullable();
            $table->unsignedSmallInteger('hdl')->nullable();
            $table->unsignedSmallInteger('ldl')->nullable();
            $table->unsignedSmallInteger('trigliceridos')->nullable();
            $table->unsignedSmallInteger('glucosa')->nullable();
            $table->boolean('fumador')->default(false);
            $table->boolean('diabetes')->default(false);
            $table->string('ecg_ritmo')->nullable();          // sinusal, FA, etc.
            $table->text('ecg_hallazgos')->nullable();
            $table->decimal('riesgo_pct', 5, 1)->nullable();   // % estimado a 10 años
            $table->string('riesgo_nivel')->nullable();        // bajo / moderado / alto / muy alto
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_cardio');
    }
};

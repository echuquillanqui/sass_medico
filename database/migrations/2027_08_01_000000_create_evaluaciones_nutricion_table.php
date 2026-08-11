<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_nutricion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();       // cm
            $table->decimal('imc', 5, 2)->nullable();
            $table->decimal('grasa', 4, 1)->nullable();       // % grasa corporal
            $table->decimal('cintura', 5, 1)->nullable();     // cm
            $table->decimal('cadera', 5, 1)->nullable();      // cm
            $table->decimal('musculo', 5, 1)->nullable();     // % masa muscular
            $table->unsignedSmallInteger('objetivo_kcal')->nullable();
            $table->decimal('peso_objetivo', 5, 2)->nullable();
            $table->text('plan')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_nutricion');
    }
};

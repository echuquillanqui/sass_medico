<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embarazos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fum')->nullable();            // fecha última menstruación
            $table->date('fpp')->nullable();            // fecha probable de parto
            $table->unsignedTinyInteger('gestas')->nullable();
            $table->unsignedTinyInteger('partos')->nullable();
            $table->unsignedTinyInteger('abortos')->nullable();
            $table->unsignedTinyInteger('cesareas')->nullable();
            $table->string('grupo_sanguineo')->nullable();
            $table->boolean('riesgo_alto')->default(false);
            $table->enum('estado', ['activo', 'finalizado'])->default('activo');
            $table->text('antecedentes')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'paciente_id']);
        });

        Schema::create('controles_prenatales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('embarazo_id')->constrained('embarazos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('semanas', 4, 1)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->string('presion_arterial')->nullable();
            $table->decimal('altura_uterina', 4, 1)->nullable();  // cm
            $table->unsignedSmallInteger('fcf')->nullable();       // latidos fetales/min
            $table->string('presentacion')->nullable();            // cefálica, podálica…
            $table->boolean('movimientos_fetales')->default(true);
            $table->boolean('edema')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles_prenatales');
        Schema::dropIfExists('embarazos');
    }
};

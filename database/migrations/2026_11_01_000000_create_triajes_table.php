<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();   // registra (enfermería)
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete(); // atiende
            $table->unsignedTinyInteger('nivel');  // 1 Rojo ... 5 Azul (Manchester)
            $table->string('motivo');
            $table->string('presion_arterial')->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->string('saturacion')->nullable();
            $table->unsignedTinyInteger('dolor')->nullable(); // 0-10
            $table->enum('estado', ['en_espera', 'en_atencion', 'atendido'])->default('en_espera');
            $table->dateTime('hora_llegada');
            $table->dateTime('hora_atencion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'estado', 'nivel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triajes');
    }
};

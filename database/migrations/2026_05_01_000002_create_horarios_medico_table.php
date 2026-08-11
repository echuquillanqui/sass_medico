<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // medico
            $table->unsignedTinyInteger('dia_semana'); // 0=domingo ... 6=sabado
            $table->string('hora_inicio');
            $table->string('hora_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['user_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_medico');
    }
};

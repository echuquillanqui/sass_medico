<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nombre')->nullable();
            $table->string('accion');            // creo | actualizo | elimino
            $table->string('modelo');            // Paciente | Cita | Pago ...
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};

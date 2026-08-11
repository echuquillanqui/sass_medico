<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('ruc')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('logo')->nullable();
            $table->enum('plan', ['basico', 'profesional', 'enterprise'])->default('basico');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('empresa_especialidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidades')->cascadeOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['empresa_id', 'especialidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_especialidad');
        Schema::dropIfExists('empresas');
    }
};

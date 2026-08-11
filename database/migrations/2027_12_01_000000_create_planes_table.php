<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('planes')) {
            return;
        }

        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->decimal('precio', 10, 2)->default(0);
            $table->enum('ciclo', ['mensual', 'anual'])->default('mensual');
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('limite_especialidades')->nullable(); // null = ilimitado
            $table->unsignedSmallInteger('limite_usuarios')->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_sangre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('donante_id')->nullable()->constrained('donantes')->nullOnDelete();
            $table->string('codigo')->nullable();
            $table->string('grupo');
            $table->integer('volumen')->default(450); // ml
            $table->date('fecha_extraccion');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['disponible', 'reservada', 'transfundida', 'descartada'])->default('disponible');
            $table->timestamps();
            $table->index(['empresa_id', 'grupo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_sangre');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_orden_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_orden_id')->constrained('lab_ordenes')->cascadeOnDelete();
            $table->foreignId('lab_examen_id')->nullable()->constrained('lab_examenes')->nullOnDelete();
            $table->string('nombre');
            $table->string('unidad')->nullable();
            $table->string('valor_referencia')->nullable();
            $table->string('resultado')->nullable();
            $table->boolean('fuera_rango')->default(false);
            $table->string('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_orden_items');
    }
};

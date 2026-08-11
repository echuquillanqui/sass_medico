<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalizacion_id')->constrained('hospitalizaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha');
            $table->text('nota');
            $table->string('presion_arterial')->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->string('saturacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evoluciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('documento')->nullable();
            $table->string('grupo');           // A+, O-, etc.
            $table->string('telefono')->nullable();
            $table->date('fecha_ultima_donacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['empresa_id', 'grupo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donantes');
    }
};

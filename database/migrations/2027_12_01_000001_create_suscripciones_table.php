<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suscripciones')) {
            return;
        }

        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ticket')->nullable()->index();
            $table->string('plan_nombre');
            $table->decimal('plan_precio', 10, 2)->default(0);
            $table->enum('ciclo', ['mensual', 'anual'])->default('mensual');
            $table->unsignedSmallInteger('duracion')->default(1);
            $table->enum('unidad', ['meses', 'anios'])->default('meses');
            $table->decimal('descuento', 10, 2)->default(0);
            $table->enum('tipo_descuento', ['monto', 'porcentaje'])->default('monto');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('metodo')->nullable();
            $table->string('nota')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispensacion_id')->constrained('dispensaciones')->cascadeOnDelete();
            $table->foreignId('insumo_id')->nullable()->constrained('insumos')->nullOnDelete();
            $table->string('nombre');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio', 10, 2)->default(0);
            $table->string('indicaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensacion_items');
    }
};

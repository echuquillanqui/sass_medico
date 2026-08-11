<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->default('info');
            $table->string('icono')->default('fa-bell');
            $table->string('titulo');
            $table->string('mensaje')->nullable();
            $table->string('url')->nullable();
            $table->boolean('leido')->default(false);
            $table->timestamps();
            $table->index(['empresa_id', 'leido']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontogramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('dientes')->nullable();      // { "18": "caries", "17": "obturado", ... }
            $table->json('plan')->nullable();         // [ { pieza, procedimiento, estado, costo }, ... ]
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->unique('paciente_id');            // un odontograma vigente por paciente
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontogramas');
    }
};

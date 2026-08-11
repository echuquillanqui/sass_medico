<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
            $table->string('concepto');
            $table->decimal('monto', 10, 2)->default(0);
            $table->enum('metodo', ['efectivo', 'tarjeta', 'transferencia', 'yape_plin', 'otro'])->default('efectivo');
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pagado');
            $table->date('fecha');
            $table->string('comprobante')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['empresa_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

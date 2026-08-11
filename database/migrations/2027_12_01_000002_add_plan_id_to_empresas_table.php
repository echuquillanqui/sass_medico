<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (! Schema::hasColumn('empresas', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('plan')->constrained('planes')->nullOnDelete();
            }
            if (! Schema::hasColumn('empresas', 'vence_suscripcion')) {
                $table->date('vence_suscripcion')->nullable()->after('plan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (Schema::hasColumn('empresas', 'plan_id')) {
                $table->dropConstrainedForeignId('plan_id');
            }
            if (Schema::hasColumn('empresas', 'vence_suscripcion')) {
                $table->dropColumn('vence_suscripcion');
            }
        });
    }
};

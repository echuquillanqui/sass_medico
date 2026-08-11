<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_examenes', function (Blueprint $table) {
            $table->foreignId('padre_id')->nullable()->after('empresa_id')
                ->constrained('lab_examenes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_examenes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('padre_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_orden_items', function (Blueprint $table) {
            $table->string('grupo')->nullable()->after('lab_examen_id');
        });
    }

    public function down(): void
    {
        Schema::table('lab_orden_items', function (Blueprint $table) {
            $table->dropColumn('grupo');
        });
    }
};

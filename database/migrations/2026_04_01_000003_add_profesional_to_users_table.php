<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cmp')->nullable()->after('telefono');           // colegiatura
            $table->string('titulo_profesional')->nullable()->after('cmp'); // Dr., Lic., etc.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cmp', 'titulo_profesional']);
        });
    }
};

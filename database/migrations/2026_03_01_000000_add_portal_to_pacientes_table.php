<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->boolean('acceso_portal')->default(false)->after('password');
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['password', 'acceso_portal', 'remember_token']);
        });
    }
};

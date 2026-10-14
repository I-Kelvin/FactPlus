<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Campos para controlar el acceso de cada usuario
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('es_admin')->default(false)->after('password');
            $table->boolean('activo')->default(true)->after('es_admin');
            // Lista de modulos que puede usar, ej: ["facturas","clientes"]
            $table->json('permisos')->nullable()->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['es_admin', 'activo', 'permisos']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            // RNC (9 digitos) o CEDULA (11 digitos)
            $table->string('tipo_documento', 10)->default('CEDULA');
            $table->string('documento', 11)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

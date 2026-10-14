<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Secuencias de Numeros de Comprobante Fiscal autorizadas por la DGII
    public function up(): void
    {
        Schema::create('secuencias_ncf', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 3)->unique(); // B01, B02, B14, B15...
            $table->string('descripcion', 100);
            $table->unsignedInteger('secuencia_actual')->default(0); // ultimo numero usado
            $table->unsignedInteger('secuencia_final');
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secuencias_ncf');
    }
};

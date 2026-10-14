<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique(); // numero interno FAC-000001
            $table->string('tipo_ncf', 3);
            $table->string('ncf', 11)->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->string('condicion', 10)->default('CONTADO'); // CONTADO o CREDITO
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estado', 10)->default('EMITIDA'); // EMITIDA o ANULADA
            $table->string('nota')->nullable();
            $table->timestamps();
        });

        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('descripcion', 150);
            $table->integer('cantidad');
            $table->decimal('precio', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_detalles');
        Schema::dropIfExists('facturas');
    }
};

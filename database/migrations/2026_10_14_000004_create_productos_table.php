<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 150);
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->decimal('precio_costo', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            // Algunos productos de la canasta basica estan exentos de ITBIS
            $table->boolean('exento_itbis')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

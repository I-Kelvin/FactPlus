<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'categoria_id', 'precio_costo', 'precio_venta',
        'stock', 'stock_minimo', 'exento_itbis', 'activo',
    ];

    protected $casts = [
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'exento_itbis' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    // Productos que llegaron al minimo o menos
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function getStockBajoAttribute()
    {
        return $this->stock <= $this->stock_minimo;
    }
}

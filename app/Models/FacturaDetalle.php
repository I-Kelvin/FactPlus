<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    protected $fillable = [
        'factura_id', 'producto_id', 'descripcion', 'cantidad', 'precio', 'descuento', 'itbis', 'total',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

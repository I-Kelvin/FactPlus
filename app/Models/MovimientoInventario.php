<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id', 'tipo', 'cantidad', 'stock_anterior', 'stock_nuevo', 'motivo', 'factura_id', 'user_id',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Registra el movimiento y actualiza el stock del producto.
    // Se usa desde inventario y desde facturacion para no repetir codigo.
    public static function registrar(Producto $producto, $tipo, $cantidad, $motivo = null, $facturaId = null)
    {
        $anterior = $producto->stock;

        if ($tipo == 'ENTRADA') {
            $nuevo = $anterior + $cantidad;
        } elseif ($tipo == 'SALIDA') {
            $nuevo = $anterior - $cantidad;
        } else {
            // En el ajuste la cantidad es el stock real contado
            $nuevo = $cantidad;
        }

        $producto->stock = $nuevo;
        $producto->save();

        return self::create([
            'producto_id' => $producto->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'stock_anterior' => $anterior,
            'stock_nuevo' => $nuevo,
            'motivo' => $motivo,
            'factura_id' => $facturaId,
            'user_id' => auth()->id(),
        ]);
    }
}

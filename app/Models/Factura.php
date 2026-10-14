<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'numero', 'tipo_ncf', 'ncf', 'cliente_id', 'user_id', 'fecha', 'condicion',
        'subtotal', 'descuento_porcentaje', 'descuento', 'itbis', 'total', 'estado', 'nota',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function scopeEmitidas($query)
    {
        return $query->where('estado', 'EMITIDA');
    }

    public function getAnuladaAttribute()
    {
        return $this->estado == 'ANULADA';
    }
}

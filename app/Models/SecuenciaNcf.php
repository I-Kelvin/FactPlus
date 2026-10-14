<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecuenciaNcf extends Model
{
    protected $table = 'secuencias_ncf';

    protected $fillable = [
        'tipo', 'descripcion', 'secuencia_actual', 'secuencia_final', 'fecha_vencimiento', 'activo',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'activo' => 'boolean',
    ];

    // Cuantos comprobantes quedan disponibles
    public function getDisponiblesAttribute()
    {
        return $this->secuencia_final - $this->secuencia_actual;
    }

    public function getVencidaAttribute()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    // El NCF tiene 11 caracteres: tipo (B02) + 8 digitos de secuencia
    public static function formatear($tipo, $numero)
    {
        return $tipo . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }
}

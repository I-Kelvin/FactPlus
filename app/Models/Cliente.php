<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre', 'tipo_documento', 'documento', 'telefono', 'correo', 'direccion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    // Muestra la cedula con guiones: 001-1234567-8, el RNC: 1-01-12345-6
    public function getDocumentoFormateadoAttribute()
    {
        $doc = $this->documento;

        if (strlen($doc) == 11) {
            return substr($doc, 0, 3) . '-' . substr($doc, 3, 7) . '-' . substr($doc, 10, 1);
        }

        if (strlen($doc) == 9) {
            return substr($doc, 0, 1) . '-' . substr($doc, 1, 2) . '-' . substr($doc, 3, 5) . '-' . substr($doc, 8, 1);
        }

        return $doc;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'rnc', 'direccion', 'telefono', 'correo', 'itbis'];

    protected $casts = [
        'itbis' => 'decimal:2',
    ];

    // Siempre trabajamos con una sola empresa (la primera)
    public static function datos()
    {
        return self::first() ?? new self(['nombre' => 'Mi Empresa', 'itbis' => 18]);
    }
}

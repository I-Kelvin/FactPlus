<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida la cedula (11 digitos) o el RNC (9 digitos) usando
 * el digito verificador, igual que lo hace la DGII.
 */
class DocumentoDominicano implements ValidationRule
{
    protected $tipo;

    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->tipo == 'CEDULA') {
            if (strlen($value) != 11 || ! self::cedulaValida($value)) {
                $fail('La cédula no es válida.');
            }
        } elseif ($this->tipo == 'RNC') {
            if (strlen($value) != 9 || ! self::rncValido($value)) {
                $fail('El RNC no es válido.');
            }
        }
    }

    // Algoritmo de Luhn (modulo 10) con pesos 1 y 2
    public static function cedulaValida($cedula)
    {
        if (! ctype_digit($cedula)) {
            return false;
        }

        $suma = 0;
        for ($i = 0; $i < 10; $i++) {
            $peso = ($i % 2 == 0) ? 1 : 2;
            $producto = $cedula[$i] * $peso;
            // si el producto tiene dos digitos se suman (ej: 14 -> 1 + 4)
            if ($producto > 9) {
                $producto = intdiv($producto, 10) + ($producto % 10);
            }
            $suma += $producto;
        }

        $verificador = (10 - ($suma % 10)) % 10;

        return $verificador == $cedula[10];
    }

    // Modulo 11 con los pesos que usa la DGII para el RNC
    public static function rncValido($rnc)
    {
        if (! ctype_digit($rnc)) {
            return false;
        }

        $pesos = [7, 9, 8, 6, 5, 4, 3, 2];
        $suma = 0;
        for ($i = 0; $i < 8; $i++) {
            $suma += $rnc[$i] * $pesos[$i];
        }

        $resto = $suma % 11;
        if ($resto == 0) {
            $verificador = 2;
        } elseif ($resto == 1) {
            $verificador = 1;
        } else {
            $verificador = 11 - $resto;
        }

        return $verificador == $rnc[8];
    }
}

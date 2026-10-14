<?php

// Funciones de ayuda que se usan en las vistas

if (! function_exists('dinero')) {
    // Formato de moneda dominicana: RD$ 1,250.00
    function dinero($monto)
    {
        return 'RD$ ' . number_format((float) $monto, 2, '.', ',');
    }
}

if (! function_exists('solo_numeros')) {
    // Quita guiones y espacios de la cedula o el RNC
    function solo_numeros($texto)
    {
        return preg_replace('/[^0-9]/', '', (string) $texto);
    }
}

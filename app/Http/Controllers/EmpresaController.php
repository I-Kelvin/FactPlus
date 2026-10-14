<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Rules\DocumentoDominicano;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::datos();

        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $request->merge(['rnc' => $request->rnc ? solo_numeros($request->rnc) : null]);

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'rnc' => ['nullable', new DocumentoDominicano(strlen((string) $request->rnc) == 11 ? 'CEDULA' : 'RNC')],
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'itbis' => 'required|numeric|min:0|max:100',
        ]);

        $empresa = Empresa::first();
        if ($empresa) {
            $empresa->update($datos);
        } else {
            Empresa::create($datos);
        }

        return back()->with('success', 'Datos de la empresa guardados.');
    }
}

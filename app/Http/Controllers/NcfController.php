<?php

namespace App\Http\Controllers;

use App\Models\SecuenciaNcf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NcfController extends Controller
{
    // Tipos de comprobantes mas usados segun la DGII
    public const TIPOS = [
        'B01' => 'Crédito Fiscal',
        'B02' => 'Consumidor Final',
        'B14' => 'Régimen Especial',
        'B15' => 'Gubernamental',
    ];

    public function index()
    {
        $secuencias = SecuenciaNcf::orderBy('tipo')->get();

        return view('ncf.index', compact('secuencias'));
    }

    public function create()
    {
        return view('ncf.create', ['secuencia' => new SecuenciaNcf(), 'tipos' => self::TIPOS]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        SecuenciaNcf::create($datos);

        return redirect()->route('ncf.index')->with('success', 'Secuencia NCF registrada.');
    }

    public function edit(SecuenciaNcf $ncf)
    {
        return view('ncf.edit', ['secuencia' => $ncf, 'tipos' => self::TIPOS]);
    }

    public function update(Request $request, SecuenciaNcf $ncf)
    {
        $datos = $this->validar($request, $ncf);

        $ncf->update($datos);

        return redirect()->route('ncf.index')->with('success', 'Secuencia NCF actualizada.');
    }

    private function validar(Request $request, $secuencia = null)
    {
        $datos = $request->validate([
            'tipo' => ['required', Rule::in(array_keys(self::TIPOS)), Rule::unique('secuencias_ncf', 'tipo')->ignore($secuencia?->id)],
            // No se puede poner una secuencia menor a la ya usada para no repetir NCF
            'secuencia_actual' => 'required|integer|min:' . ($secuencia ? $secuencia->secuencia_actual : 0),
            'secuencia_final' => 'required|integer|gt:secuencia_actual|max:99999999',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $datos['descripcion'] = self::TIPOS[$datos['tipo']];
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}

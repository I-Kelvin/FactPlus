<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Rules\DocumentoDominicano;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $clientes = Cliente::when($buscar, function ($query) use ($buscar) {
            $query->where('nombre', 'ilike', "%$buscar%");

            // Si escribio numeros tambien se busca por cedula/RNC
            if (solo_numeros($buscar) != '') {
                $query->orWhere('documento', 'like', '%' . solo_numeros($buscar) . '%');
            }
        })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'buscar'));
    }

    public function create()
    {
        return view('clientes.create', ['cliente' => new Cliente()]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Cliente::create($datos);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $facturas = $cliente->facturas()->latest()->paginate(10);

        return view('clientes.show', compact('cliente', 'facturas'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $this->validar($request, $cliente->id);

        $cliente->update($datos);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        // Si ya tiene facturas no se puede borrar, solo desactivar
        if ($cliente->facturas()->exists()) {
            $cliente->update(['activo' => false]);

            return back()->with('warning', 'El cliente tiene facturas, por eso solo se desactivó.');
        }

        $cliente->delete();

        return back()->with('success', 'Cliente eliminado.');
    }

    // Validacion compartida entre store y update
    private function validar(Request $request, $id = null)
    {
        // Se guardan solo los numeros del documento (sin guiones)
        $request->merge([
            'documento' => $request->documento ? solo_numeros($request->documento) : null,
        ]);

        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'tipo_documento' => 'required|in:CEDULA,RNC,NINGUNO',
            'documento' => [
                'nullable',
                'required_unless:tipo_documento,NINGUNO',
                Rule::unique('clientes', 'documento')->ignore($id),
                new DocumentoDominicano($request->tipo_documento),
            ],
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($datos['tipo_documento'] == 'NINGUNO') {
            $datos['documento'] = null;
        }

        $datos['activo'] = $request->boolean('activo', true);

        return $datos;
    }
}

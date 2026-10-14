<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo');
        $productoId = $request->get('producto_id');

        $movimientos = MovimientoInventario::with('producto', 'usuario', 'factura')
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->when($productoId, fn ($q) => $q->where('producto_id', $productoId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $productos = Producto::orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        return view('inventario.index', compact('movimientos', 'productos', 'tipo', 'productoId'));
    }

    public function create(Request $request)
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $seleccionado = $request->get('producto_id');

        return view('inventario.create', compact('productos', 'seleccionado'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:ENTRADA,SALIDA,AJUSTE',
            'cantidad' => 'required|integer|min:' . ($request->tipo == 'AJUSTE' ? 0 : 1),
            'motivo' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($datos) {
            $producto = Producto::lockForUpdate()->find($datos['producto_id']);

            if ($datos['tipo'] == 'SALIDA' && $datos['cantidad'] > $producto->stock) {
                throw ValidationException::withMessages([
                    'cantidad' => "Solo hay {$producto->stock} unidades disponibles de {$producto->nombre}.",
                ]);
            }

            MovimientoInventario::registrar($producto, $datos['tipo'], $datos['cantidad'], $datos['motivo']);
        });

        return redirect()->route('inventario.index')->with('success', 'Movimiento registrado correctamente.');
    }
}

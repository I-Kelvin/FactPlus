<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $categoriaId = $request->get('categoria_id');

        $productos = Producto::with('categoria')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'ilike', "%$buscar%")
                        ->orWhere('codigo', 'ilike', "%$buscar%");
                });
            })
            ->when($categoriaId, function ($query) use ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            })
            ->when($request->get('stock_bajo'), function ($query) {
                $query->stockBajo();
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'buscar', 'categoriaId'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.create', ['producto' => new Producto(['stock_minimo' => 5]), 'categorias' => $categorias]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:30|unique:productos,codigo',
            'nombre' => 'required|string|max:150',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gte:precio_costo',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $datos['exento_itbis'] = $request->boolean('exento_itbis');
        $datos['activo'] = true;

        DB::transaction(function () use ($datos) {
            $stockInicial = $datos['stock'];
            $datos['stock'] = 0;

            $producto = Producto::create($datos);

            // El stock inicial queda registrado en el kardex
            if ($stockInicial > 0) {
                MovimientoInventario::registrar($producto, 'ENTRADA', $stockInicial, 'Inventario inicial');
            }
        });

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }

    public function show(Producto $producto)
    {
        $movimientos = $producto->movimientos()->with('usuario', 'factura')->latest()->paginate(10);

        return view('productos.show', compact('producto', 'movimientos'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        // El stock no se cambia aqui, se cambia desde Inventario para que quede el historial
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:30', Rule::unique('productos', 'codigo')->ignore($producto->id)],
            'nombre' => 'required|string|max:150',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gte:precio_costo',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $datos['exento_itbis'] = $request->boolean('exento_itbis');
        $datos['activo'] = $request->boolean('activo');

        $producto->update($datos);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->detalles()->exists()) {
            $producto->update(['activo' => false]);

            return back()->with('warning', 'El producto ya fue facturado, por eso solo se desactivó.');
        }

        $producto->delete();

        return back()->with('success', 'Producto eliminado.');
    }
}

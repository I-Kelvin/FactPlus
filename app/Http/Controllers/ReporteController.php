<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        // Por defecto se muestra el mes actual
        $desde = $request->get('desde', Carbon::today()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', Carbon::today()->toDateString());

        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $facturas = Factura::with('cliente')
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        // Los totales solo cuentan las facturas emitidas, no las anuladas
        $emitidas = $facturas->where('estado', 'EMITIDA');
        $totales = [
            'cantidad' => $emitidas->count(),
            'anuladas' => $facturas->where('estado', 'ANULADA')->count(),
            'subtotal' => $emitidas->sum('subtotal'),
            'descuento' => $emitidas->sum('descuento'),
            'itbis' => $emitidas->sum('itbis'),
            'total' => $emitidas->sum('total'),
            'contado' => $emitidas->where('condicion', 'CONTADO')->sum('total'),
            'credito' => $emitidas->where('condicion', 'CREDITO')->sum('total'),
        ];

        return view('reportes.ventas', compact('facturas', 'totales', 'desde', 'hasta'));
    }

    public function inventario(Request $request)
    {
        $categoriaId = $request->get('categoria_id');

        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->when($categoriaId, fn ($q) => $q->where('categoria_id', $categoriaId))
            ->orderBy('nombre')
            ->get();

        $totales = [
            'unidades' => $productos->sum('stock'),
            'costo' => $productos->sum(fn ($p) => $p->stock * $p->precio_costo),
            'venta' => $productos->sum(fn ($p) => $p->stock * $p->precio_venta),
        ];

        $categorias = Categoria::orderBy('nombre')->get();

        return view('reportes.inventario', compact('productos', 'totales', 'categorias', 'categoriaId'));
    }
}

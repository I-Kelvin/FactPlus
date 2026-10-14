<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\SecuenciaNcf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $estado = $request->get('estado');

        $facturas = Factura::with('cliente')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('numero', 'ilike', "%$buscar%")
                        ->orWhere('ncf', 'ilike', "%$buscar%")
                        ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'ilike', "%$buscar%"));
                });
            })
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('facturas.index', compact('facturas', 'buscar', 'estado'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->where('stock', '>', 0)->orderBy('nombre')->get();
        $secuencias = SecuenciaNcf::where('activo', true)->orderBy('tipo')->get();
        $empresa = Empresa::datos();

        // Datos que usa el JavaScript de la vista para calcular los totales
        $listaProductos = [];
        foreach ($productos as $p) {
            $listaProductos[$p->id] = [
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio_venta,
                'stock' => $p->stock,
                'exento' => $p->exento_itbis,
            ];
        }

        return view('facturas.create', compact('clientes', 'productos', 'listaProductos', 'secuencias', 'empresa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_ncf' => 'required|exists:secuencias_ncf,tipo',
            'fecha' => 'required|date|before_or_equal:today',
            'condicion' => 'required|in:CONTADO,CREDITO',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'nota' => 'nullable|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Debe agregar al menos un producto a la factura.',
        ]);

        $cliente = $request->cliente_id ? Cliente::find($request->cliente_id) : null;

        // Para credito fiscal (B01) el cliente debe tener RNC o cedula
        if ($request->tipo_ncf != 'B02' && (! $cliente || ! $cliente->documento)) {
            throw ValidationException::withMessages([
                'cliente_id' => 'Para este tipo de comprobante debe elegir un cliente con RNC o cédula.',
            ]);
        }

        if ($request->condicion == 'CREDITO' && ! $cliente) {
            throw ValidationException::withMessages([
                'cliente_id' => 'Las ventas a crédito necesitan un cliente.',
            ]);
        }

        // Si el mismo producto viene en dos lineas se suman las cantidades
        $lineas = [];
        foreach ($request->productos as $item) {
            $id = $item['id'];
            $lineas[$id] = ($lineas[$id] ?? 0) + (int) $item['cantidad'];
        }

        $factura = DB::transaction(function () use ($request, $lineas) {
            // Bloqueamos la empresa para que dos facturas no salgan con el mismo numero
            $empresa = Empresa::lockForUpdate()->first();
            $tasaItbis = $empresa ? $empresa->itbis : 18;

            $secuencia = SecuenciaNcf::where('tipo', $request->tipo_ncf)->lockForUpdate()->first();

            if (! $secuencia->activo || $secuencia->vencida || $secuencia->disponibles <= 0) {
                throw ValidationException::withMessages([
                    'tipo_ncf' => "La secuencia {$secuencia->tipo} está vencida, inactiva o agotada.",
                ]);
            }

            $porcentajeDesc = (float) ($request->descuento_porcentaje ?? 0);
            $subtotal = 0;
            $totalDescuento = 0;
            $totalItbis = 0;
            $detalles = [];

            foreach ($lineas as $productoId => $cantidad) {
                $producto = Producto::lockForUpdate()->find($productoId);

                if ($cantidad > $producto->stock) {
                    throw ValidationException::withMessages([
                        'productos' => "No hay suficiente existencia de {$producto->nombre}. Disponible: {$producto->stock}.",
                    ]);
                }

                $importe = round($producto->precio_venta * $cantidad, 2);
                $descuento = round($importe * $porcentajeDesc / 100, 2);
                // El ITBIS se calcula despues de aplicar el descuento
                $itbis = $producto->exento_itbis ? 0 : round(($importe - $descuento) * $tasaItbis / 100, 2);

                $subtotal += $importe;
                $totalDescuento += $descuento;
                $totalItbis += $itbis;

                $detalles[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio' => $producto->precio_venta,
                    'descuento' => $descuento,
                    'itbis' => $itbis,
                    'total' => $importe - $descuento + $itbis,
                ];
            }

            // Tomamos el siguiente NCF de la secuencia
            $secuencia->secuencia_actual = $secuencia->secuencia_actual + 1;
            $secuencia->save();

            $siguiente = (Factura::max('id') ?? 0) + 1;

            $factura = Factura::create([
                'numero' => 'FAC-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT),
                'tipo_ncf' => $secuencia->tipo,
                'ncf' => SecuenciaNcf::formatear($secuencia->tipo, $secuencia->secuencia_actual),
                'cliente_id' => $request->cliente_id,
                'user_id' => auth()->id(),
                'fecha' => $request->fecha,
                'condicion' => $request->condicion,
                'subtotal' => $subtotal,
                'descuento_porcentaje' => $porcentajeDesc,
                'descuento' => $totalDescuento,
                'itbis' => $totalItbis,
                'total' => $subtotal - $totalDescuento + $totalItbis,
                'estado' => 'EMITIDA',
                'nota' => $request->nota,
            ]);

            foreach ($detalles as $d) {
                $factura->detalles()->create([
                    'producto_id' => $d['producto']->id,
                    'descripcion' => $d['producto']->nombre,
                    'cantidad' => $d['cantidad'],
                    'precio' => $d['precio'],
                    'descuento' => $d['descuento'],
                    'itbis' => $d['itbis'],
                    'total' => $d['total'],
                ]);

                // Salida de inventario
                MovimientoInventario::registrar($d['producto'], 'SALIDA', $d['cantidad'], 'Venta ' . $factura->numero, $factura->id);
            }

            return $factura;
        });

        return redirect()->route('facturas.show', $factura)->with('success', "Factura {$factura->numero} creada con NCF {$factura->ncf}.");
    }

    public function show(Factura $factura)
    {
        $factura->load('cliente', 'detalles', 'usuario');

        return view('facturas.show', compact('factura'));
    }

    public function imprimir(Factura $factura)
    {
        $factura->load('cliente', 'detalles', 'usuario');
        $empresa = Empresa::datos();

        return view('facturas.imprimir', compact('factura', 'empresa'));
    }

    public function anular(Request $request, Factura $factura)
    {
        $request->validate([
            'motivo' => 'required|string|max:200',
        ]);

        if ($factura->anulada) {
            return back()->with('warning', 'Esta factura ya estaba anulada.');
        }

        DB::transaction(function () use ($factura, $request) {
            $factura->estado = 'ANULADA';
            $factura->nota = mb_substr(trim('ANULADA: ' . $request->motivo . ' | ' . $factura->nota, ' |'), 0, 255);
            $factura->save();

            // Se devuelven los productos al inventario.
            // El NCF no se reutiliza, queda como anulado (asi lo pide la DGII).
            foreach ($factura->detalles as $detalle) {
                $producto = Producto::lockForUpdate()->find($detalle->producto_id);
                MovimientoInventario::registrar($producto, 'ENTRADA', $detalle->cantidad, 'Anulación ' . $factura->numero, $factura->id);
            }
        });

        return back()->with('success', "La factura {$factura->numero} fue anulada y el inventario se restauró.");
    }
}

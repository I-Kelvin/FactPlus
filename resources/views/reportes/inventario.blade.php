@extends('layouts.app')

@section('titulo', 'Inventario valorizado')

@section('contenido')
<div class="card mb-3 d-print-none">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-auto">
                <label class="form-label small">Categoría</label>
                <select name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach ($categorias as $c)
                        <option value="{{ $c->id }}" @selected($categoriaId == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Consultar</button>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> Imprimir</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card tarjeta-resumen p-3"><small class="text-muted">Unidades en existencia</small><h5 class="mb-0">{{ number_format($totales['unidades']) }}</h5></div></div>
    <div class="col-md-4"><div class="card tarjeta-resumen p-3"><small class="text-muted">Valor al costo</small><h5 class="mb-0">{{ dinero($totales['costo']) }}</h5></div></div>
    <div class="col-md-4"><div class="card tarjeta-resumen p-3"><small class="text-muted">Valor a precio de venta</small><h5 class="mb-0">{{ dinero($totales['venta']) }}</h5>
        <small class="text-muted">Ganancia estimada {{ dinero($totales['venta'] - $totales['costo']) }}</small></div></div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Existencia</th>
                    <th class="text-end">Costo</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Valor costo</th>
                    <th class="text-end">Valor venta</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $p)
                    <tr>
                        <td>{{ $p->codigo }}</td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->categoria->nombre ?? '—' }}</td>
                        <td class="text-center {{ $p->stock_bajo ? 'text-danger fw-bold' : '' }}">{{ $p->stock }}</td>
                        <td class="text-end">{{ number_format($p->precio_costo, 2) }}</td>
                        <td class="text-end">{{ number_format($p->precio_venta, 2) }}</td>
                        <td class="text-end">{{ number_format($p->stock * $p->precio_costo, 2) }}</td>
                        <td class="text-end">{{ number_format($p->stock * $p->precio_venta, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No hay productos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

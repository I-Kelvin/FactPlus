@extends('layouts.app')

@section('titulo', 'Movimientos de inventario')

@section('contenido')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <select name="producto_id" class="form-select form-select-sm" style="width: 230px">
                <option value="">Todos los productos</option>
                @foreach ($productos as $p)
                    <option value="{{ $p->id }}" @selected($productoId == $p->id)>{{ $p->codigo }} - {{ $p->nombre }}</option>
                @endforeach
            </select>
            <select name="tipo" class="form-select form-select-sm" style="width: 140px">
                <option value="">Todos los tipos</option>
                @foreach (['ENTRADA' => 'Entradas', 'SALIDA' => 'Salidas', 'AJUSTE' => 'Ajustes'] as $valor => $texto)
                    <option value="{{ $valor }}" @selected($tipo == $valor)>{{ $texto }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-funnel"></i> Filtrar</button>
        </form>
        <a href="{{ route('inventario.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nuevo movimiento</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Antes</th>
                    <th class="text-center">Después</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $mov)
                    <tr>
                        <td class="text-nowrap">{{ $mov->created_at->format('d/m/Y h:i A') }}</td>
                        <td>
                            @can('productos')
                                <a href="{{ route('productos.show', $mov->producto) }}">{{ $mov->producto->nombre }}</a>
                            @else
                                {{ $mov->producto->nombre }}
                            @endcan
                        </td>
                        <td>@include('inventario._tipo')</td>
                        <td class="text-center">{{ $mov->cantidad }}</td>
                        <td class="text-center">{{ $mov->stock_anterior }}</td>
                        <td class="text-center">{{ $mov->stock_nuevo }}</td>
                        <td>
                            @if ($mov->factura && auth()->user()->can('facturas'))
                                <a href="{{ route('facturas.show', $mov->factura) }}">{{ $mov->motivo }}</a>
                            @else
                                {{ $mov->motivo }}
                            @endif
                        </td>
                        <td>{{ $mov->usuario->name ?? 'Sistema' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No hay movimientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $movimientos->links() }}</div>
</div>
@endsection

@extends('layouts.app')

@section('titulo', 'Producto')

@section('contenido')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>{{ $producto->codigo }}</span>
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            </div>
            <div class="card-body">
                <h5>{{ $producto->nombre }}</h5>
                <p class="mb-1"><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? '—' }}</p>
                <p class="mb-1"><strong>Costo:</strong> {{ dinero($producto->precio_costo) }}</p>
                <p class="mb-1"><strong>Precio:</strong> {{ dinero($producto->precio_venta) }}</p>
                <p class="mb-1"><strong>ITBIS:</strong> {{ $producto->exento_itbis ? 'Exento' : 'Gravado' }}</p>
                <p class="mb-1"><strong>Existencia:</strong>
                    <span class="badge bg-{{ $producto->stock_bajo ? 'danger' : 'success' }}">{{ $producto->stock }}</span>
                    (mínimo {{ $producto->stock_minimo }})
                </p>
                @can('inventario')
                    <a href="{{ route('inventario.create', ['producto_id' => $producto->id]) }}" class="btn btn-sm btn-success mt-2">
                        <i class="bi bi-box-arrow-in-down"></i> Registrar movimiento
                    </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Kardex (historial de movimientos)</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr><th>Fecha</th><th>Tipo</th><th class="text-center">Cant.</th><th class="text-center">Antes</th><th class="text-center">Después</th><th>Motivo</th><th>Usuario</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($movimientos as $mov)
                            <tr>
                                <td>{{ $mov->created_at->format('d/m/Y h:i A') }}</td>
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
                            <tr><td colspan="7" class="text-center text-muted">Sin movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $movimientos->links() }}</div>
        </div>
    </div>
</div>
@endsection

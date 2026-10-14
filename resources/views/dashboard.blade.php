@extends('layouts.app')

@section('titulo', 'Inicio')

@section('contenido')
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card tarjeta-resumen p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <small class="text-muted">Ventas de hoy</small>
                    <h4 class="mb-0">{{ dinero($ventasHoy) }}</h4>
                </div>
                <i class="bi bi-cash-coin icono"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card tarjeta-resumen p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <small class="text-muted">Ventas del mes</small>
                    <h4 class="mb-0">{{ dinero($ventasMes) }}</h4>
                    <small class="text-muted">{{ $facturasMes }} facturas</small>
                </div>
                <i class="bi bi-calendar-check icono"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card tarjeta-resumen p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <small class="text-muted">Productos activos</small>
                    <h4 class="mb-0">{{ $totalProductos }}</h4>
                </div>
                <i class="bi bi-box-seam icono"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card tarjeta-resumen p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <small class="text-muted">Clientes</small>
                    <h4 class="mb-0">{{ $totalClientes }}</h4>
                </div>
                <i class="bi bi-people icono"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Ventas de los últimos 7 días</div>
            <div class="card-body">
                <canvas id="graficaVentas" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-exclamation-triangle text-danger"></i> Stock bajo</span>
                @can('productos')
                    <a href="{{ route('productos.index', ['stock_bajo' => 1]) }}" class="small">Ver todos</a>
                @endcan
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($stockBajo as $producto)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $producto->nombre }}</span>
                        <span class="badge bg-danger">{{ $producto->stock }} / mín. {{ $producto->stock_minimo }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Todo el inventario está en orden.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <span>Últimas facturas</span>
        @can('facturas')
            <a href="{{ route('facturas.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nueva factura</a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>NCF</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ultimasFacturas as $factura)
                    <tr>
                        <td>
                            @can('facturas')
                                <a href="{{ route('facturas.show', $factura) }}">{{ $factura->numero }}</a>
                            @else
                                {{ $factura->numero }}
                            @endcan
                        </td>
                        <td>{{ $factura->ncf }}</td>
                        <td>{{ $factura->cliente->nombre ?? 'Consumidor final' }}</td>
                        <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                        <td class="text-end">{{ dinero($factura->total) }}</td>
                        <td>@include('facturas._estado')</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Todavía no hay facturas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('graficaVentas'), {
        type: 'bar',
        data: {
            labels: @json($dias),
            datasets: [{
                label: 'Ventas RD$',
                data: @json($montos),
                backgroundColor: '#f08c2e',
                borderRadius: 4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush

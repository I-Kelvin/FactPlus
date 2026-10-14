@extends('layouts.app')

@section('titulo', 'Cliente')

@section('contenido')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Datos del cliente</span>
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            </div>
            <div class="card-body">
                <h5>{{ $cliente->nombre }}</h5>
                <p class="mb-1"><strong>{{ $cliente->tipo_documento }}:</strong> {{ $cliente->documento_formateado ?? '—' }}</p>
                <p class="mb-1"><strong>Teléfono:</strong> {{ $cliente->telefono ?? '—' }}</p>
                <p class="mb-1"><strong>Correo:</strong> {{ $cliente->correo ?? '—' }}</p>
                <p class="mb-0"><strong>Dirección:</strong> {{ $cliente->direccion ?? '—' }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Facturas del cliente</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Número</th><th>NCF</th><th>Fecha</th><th>Condición</th><th class="text-end">Total</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($facturas as $factura)
                            <tr>
                                <td>
                                    @can('facturas')
                                        <a href="{{ route('facturas.show', $factura) }}">{{ $factura->numero }}</a>
                                    @else
                                        {{ $factura->numero }}
                                    @endcan
                                </td>
                                <td>{{ $factura->ncf }}</td>
                                <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                                <td>{{ $factura->condicion }}</td>
                                <td class="text-end">{{ dinero($factura->total) }}</td>
                                <td>@include('facturas._estado')</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Este cliente no tiene facturas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $facturas->links() }}</div>
        </div>
    </div>
</div>
@endsection

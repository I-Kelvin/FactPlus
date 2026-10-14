@extends('layouts.app')

@section('titulo', 'Factura ' . $factura->numero)

@section('contenido')
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('facturas.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
    <a href="{{ route('facturas.imprimir', $factura) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-printer"></i> Imprimir</a>
    @if (! $factura->anulada && auth()->user()->can('anular_facturas'))
        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnular"><i class="bi bi-x-circle"></i> Anular</button>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h4 class="mb-1">{{ $factura->numero }} @include('facturas._estado')</h4>
                <p class="mb-0"><strong>NCF:</strong> {{ $factura->ncf }} ({{ $factura->tipo_ncf == 'B01' ? 'Crédito Fiscal' : ($factura->tipo_ncf == 'B02' ? 'Consumidor Final' : $factura->tipo_ncf) }})</p>
                <p class="mb-0"><strong>Fecha:</strong> {{ $factura->fecha->format('d/m/Y') }}</p>
                <p class="mb-0"><strong>Condición:</strong> {{ ucfirst(strtolower($factura->condicion)) }}</p>
                <p class="mb-0"><strong>Vendedor:</strong> {{ $factura->usuario->name ?? '' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="text-muted mb-1">Cliente</h6>
                @if ($factura->cliente)
                    <p class="mb-0">
                        @can('clientes')
                            <a href="{{ route('clientes.show', $factura->cliente) }}">{{ $factura->cliente->nombre }}</a>
                        @else
                            {{ $factura->cliente->nombre }}
                        @endcan
                    </p>
                    @if ($factura->cliente->documento)
                        <p class="mb-0">{{ $factura->cliente->tipo_documento }}: {{ $factura->cliente->documento_formateado }}</p>
                    @endif
                    <p class="mb-0">{{ $factura->cliente->direccion }}</p>
                @else
                    <p class="mb-0">Consumidor final</p>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Descuento</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($factura->detalles as $d)
                        <tr>
                            <td>{{ $d->descripcion }}</td>
                            <td class="text-center">{{ $d->cantidad }}</td>
                            <td class="text-end">{{ dinero($d->precio) }}</td>
                            <td class="text-end">{{ dinero($d->descuento) }}</td>
                            <td class="text-end">{{ dinero($d->itbis) }}</td>
                            <td class="text-end">{{ dinero($d->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6">
                @if ($factura->nota)
                    <p class="text-muted"><strong>Nota:</strong> {{ $factura->nota }}</p>
                @endif
            </div>
            <div class="col-md-6 d-flex justify-content-md-end">
                <table class="totales-factura">
                    <tr><td>Subtotal:</td><td class="text-end">{{ dinero($factura->subtotal) }}</td></tr>
                    @if ($factura->descuento > 0)
                        <tr><td>Descuento ({{ (float) $factura->descuento_porcentaje }}%):</td><td class="text-end text-danger">- {{ dinero($factura->descuento) }}</td></tr>
                    @endif
                    <tr><td>ITBIS:</td><td class="text-end">{{ dinero($factura->itbis) }}</td></tr>
                    <tr class="fs-5 fw-bold"><td>Total:</td><td class="text-end">{{ dinero($factura->total) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Ventana para anular (solo para quien tiene el permiso) --}}
@can('anular_facturas')
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('facturas.anular', $factura) }}" method="POST" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Anular factura {{ $factura->numero }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Los productos volverán al inventario y el NCF <strong>{{ $factura->ncf }}</strong> quedará anulado (no se vuelve a usar).</p>
                <label class="form-label">Motivo *</label>
                <input type="text" name="motivo" class="form-control" required maxlength="200">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger">Anular factura</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

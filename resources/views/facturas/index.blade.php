@extends('layouts.app')

@section('titulo', 'Facturas')

@section('contenido')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <input type="text" name="buscar" class="form-control form-control-sm" style="width: 220px" placeholder="Número, NCF o cliente" value="{{ $buscar }}">
            <select name="estado" class="form-select form-select-sm" style="width: 140px">
                <option value="">Todas</option>
                <option value="EMITIDA" @selected($estado == 'EMITIDA')>Emitidas</option>
                <option value="ANULADA" @selected($estado == 'ANULADA')>Anuladas</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('facturas.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nueva factura</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>NCF</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Condición</th>
                    <th class="text-end">ITBIS</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facturas as $factura)
                    <tr>
                        <td><a href="{{ route('facturas.show', $factura) }}">{{ $factura->numero }}</a></td>
                        <td>{{ $factura->ncf }}</td>
                        <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                        <td>{{ $factura->cliente->nombre ?? 'Consumidor final' }}</td>
                        <td>{{ ucfirst(strtolower($factura->condicion)) }}</td>
                        <td class="text-end">{{ dinero($factura->itbis) }}</td>
                        <td class="text-end">{{ dinero($factura->total) }}</td>
                        <td>@include('facturas._estado')</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('facturas.show', $factura) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('facturas.imprimir', $factura) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No se encontraron facturas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $facturas->links() }}</div>
</div>
@endsection

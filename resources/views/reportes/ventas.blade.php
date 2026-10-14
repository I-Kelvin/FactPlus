@extends('layouts.app')

@section('titulo', 'Reporte de ventas')

@section('contenido')
<div class="card mb-3 d-print-none">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-auto">
                <label class="form-label small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $desde }}">
            </div>
            <div class="col-auto">
                <label class="form-label small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $hasta }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Consultar</button>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> Imprimir</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card tarjeta-resumen p-3"><small class="text-muted">Facturas emitidas</small><h5 class="mb-0">{{ $totales['cantidad'] }}</h5><small class="text-muted">{{ $totales['anuladas'] }} anuladas</small></div></div>
    <div class="col-6 col-md-3"><div class="card tarjeta-resumen p-3"><small class="text-muted">Subtotal</small><h5 class="mb-0">{{ dinero($totales['subtotal'] - $totales['descuento']) }}</h5></div></div>
    <div class="col-6 col-md-3"><div class="card tarjeta-resumen p-3"><small class="text-muted">ITBIS facturado</small><h5 class="mb-0">{{ dinero($totales['itbis']) }}</h5></div></div>
    <div class="col-6 col-md-3"><div class="card tarjeta-resumen p-3"><small class="text-muted">Total vendido</small><h5 class="mb-0">{{ dinero($totales['total']) }}</h5>
        <small class="text-muted">Contado {{ dinero($totales['contado']) }} · Crédito {{ dinero($totales['credito']) }}</small></div></div>
</div>

<div class="card">
    <div class="card-header">Ventas del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Número</th>
                    <th>NCF</th>
                    <th>Cliente</th>
                    <th>RNC/Cédula</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Desc.</th>
                    <th class="text-end">ITBIS</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facturas as $factura)
                    <tr class="{{ $factura->anulada ? 'text-decoration-line-through text-muted' : '' }}">
                        <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                        <td>
                            @can('facturas')
                                <a href="{{ route('facturas.show', $factura) }}">{{ $factura->numero }}</a>
                            @else
                                {{ $factura->numero }}
                            @endcan
                        </td>
                        <td>{{ $factura->ncf }}</td>
                        <td>{{ $factura->cliente->nombre ?? 'Consumidor final' }}</td>
                        <td>{{ $factura->cliente->documento ?? '' }}</td>
                        <td class="text-end">{{ number_format($factura->subtotal, 2) }}</td>
                        <td class="text-end">{{ number_format($factura->descuento, 2) }}</td>
                        <td class="text-end">{{ number_format($factura->itbis, 2) }}</td>
                        <td class="text-end">{{ number_format($factura->total, 2) }}</td>
                        <td>@include('facturas._estado')</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted">No hay ventas en ese rango de fechas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

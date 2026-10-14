@extends('layouts.app')

@section('titulo', 'Comprobantes fiscales (NCF)')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Secuencias autorizadas por la DGII</span>
        <a href="{{ route('ncf.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nueva secuencia</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Último usado</th>
                    <th>Próximo NCF</th>
                    <th class="text-center">Disponibles</th>
                    <th>Vence</th>
                    <th>Estado</th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($secuencias as $s)
                    <tr>
                        <td><strong>{{ $s->tipo }}</strong></td>
                        <td>{{ $s->descripcion }}</td>
                        <td>{{ $s->secuencia_actual > 0 ? \App\Models\SecuenciaNcf::formatear($s->tipo, $s->secuencia_actual) : '—' }}</td>
                        <td>{{ \App\Models\SecuenciaNcf::formatear($s->tipo, $s->secuencia_actual + 1) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $s->disponibles < 20 ? 'warning text-dark' : 'info' }}">{{ $s->disponibles }}</span>
                        </td>
                        <td class="{{ $s->vencida ? 'text-danger' : '' }}">{{ $s->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                        <td>
                            @if (! $s->activo)
                                <span class="badge bg-secondary">Inactiva</span>
                            @elseif ($s->vencida)
                                <span class="badge bg-danger">Vencida</span>
                            @elseif ($s->disponibles <= 0)
                                <span class="badge bg-danger">Agotada</span>
                            @else
                                <span class="badge bg-success">Activa</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ncf.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No hay secuencias registradas. Debe registrar al menos la B02 para poder facturar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

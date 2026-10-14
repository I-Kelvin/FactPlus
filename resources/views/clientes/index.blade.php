@extends('layouts.app')

@section('titulo', 'Clientes')

@section('contenido')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Nombre, cédula o RNC" value="{{ $buscar }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nuevo cliente</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        <td><a href="{{ route('clientes.show', $cliente) }}">{{ $cliente->nombre }}</a></td>
                        <td>
                            @if ($cliente->documento)
                                <small class="text-muted">{{ $cliente->tipo_documento }}</small> {{ $cliente->documento_formateado }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>{{ $cliente->correo }}</td>
                        <td>
                            <span class="badge bg-{{ $cliente->activo ? 'success' : 'secondary' }}">{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline confirmar">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No se encontraron clientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $clientes->links() }}</div>
</div>
@endsection

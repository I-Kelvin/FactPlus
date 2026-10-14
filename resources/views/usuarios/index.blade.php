@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Usuarios del sistema</span>
        <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nuevo usuario</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Acceso</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr class="{{ $usuario->activo ? '' : 'text-muted' }}">
                        <td>
                            {{ $usuario->name }}
                            @if ($usuario->id == auth()->id()) <span class="badge bg-light text-dark">Usted</span> @endif
                        </td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if ($usuario->es_admin)
                                <span class="badge bg-dark">Administrador (todo)</span>
                            @else
                                @forelse ($usuario->permisos ?? [] as $permiso)
                                    <span class="badge bg-info text-dark">{{ \App\Models\User::PERMISOS[$permiso] ?? $permiso }}</span>
                                @empty
                                    <span class="text-muted small">Solo inicio</span>
                                @endforelse
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @if ($usuario->id != auth()->id())
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline confirmar"
                                      data-mensaje="¿Eliminar el usuario {{ $usuario->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $usuarios->links() }}</div>
</div>
@endsection

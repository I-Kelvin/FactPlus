@extends('layouts.app')

@section('titulo', 'Categorías')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Listado de categorías</span>
        <a href="{{ route('categorias.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nueva categoría</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Productos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->nombre }}</td>
                        <td>{{ $categoria->descripcion }}</td>
                        <td class="text-center">
                            <a href="{{ route('productos.index', ['categoria_id' => $categoria->id]) }}" class="badge bg-info text-decoration-none">{{ $categoria->productos_count }}</a>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline confirmar"
                                  data-mensaje="¿Eliminar la categoría? Los productos quedarán sin categoría.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No hay categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $categorias->links() }}</div>
</div>
@endsection

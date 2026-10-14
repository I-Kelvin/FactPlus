@extends('layouts.app')

@section('titulo', 'Productos')

@section('contenido')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <input type="text" name="buscar" class="form-control form-control-sm" style="width: 200px" placeholder="Código o nombre" value="{{ $buscar }}">
            <select name="categoria_id" class="form-select form-select-sm" style="width: 170px">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected($categoriaId == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            <div class="form-check align-self-center">
                <input type="checkbox" name="stock_bajo" value="1" id="stock_bajo" class="form-check-input" @checked(request('stock_bajo'))>
                <label for="stock_bajo" class="form-check-label small">Solo stock bajo</label>
            </div>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i> Filtrar</button>
        </form>
        <a href="{{ route('productos.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Nuevo producto</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-end">Costo</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Existencia</th>
                    <th>ITBIS</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr class="{{ $producto->activo ? '' : 'text-muted' }}">
                        <td>{{ $producto->codigo }}</td>
                        <td>
                            <a href="{{ route('productos.show', $producto) }}">{{ $producto->nombre }}</a>
                            @unless ($producto->activo) <span class="badge bg-secondary">Inactivo</span> @endunless
                        </td>
                        <td>{{ $producto->categoria->nombre ?? '—' }}</td>
                        <td class="text-end">{{ dinero($producto->precio_costo) }}</td>
                        <td class="text-end">{{ dinero($producto->precio_venta) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $producto->stock_bajo ? 'danger' : 'success' }}">{{ $producto->stock }}</span>
                        </td>
                        <td>{{ $producto->exento_itbis ? 'Exento' : '18%' }}</td>
                        <td class="text-end text-nowrap">
                            @can('inventario')
                                <a href="{{ route('inventario.create', ['producto_id' => $producto->id]) }}" class="btn btn-sm btn-outline-success" title="Entrada / ajuste"><i class="bi bi-box-arrow-in-down"></i></a>
                            @endcan
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline confirmar">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No se encontraron productos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $productos->links() }}</div>
</div>
@endsection

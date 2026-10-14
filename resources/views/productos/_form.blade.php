{{-- Formulario compartido de productos --}}
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo', $producto->codigo) }}" required>
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre) }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select">
            <option value="">-- Sin categoría --</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $producto->categoria_id) == $categoria->id)>{{ $categoria->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Precio de costo (RD$) *</label>
        <input type="number" step="0.01" min="0" name="precio_costo" class="form-control @error('precio_costo') is-invalid @enderror" value="{{ old('precio_costo', $producto->precio_costo) }}" required>
        @error('precio_costo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Precio de venta (RD$) *</label>
        <input type="number" step="0.01" min="0" name="precio_venta" class="form-control @error('precio_venta') is-invalid @enderror" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
        @error('precio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    @if (! $producto->exists)
        <div class="col-md-3">
            <label class="form-label">Existencia inicial *</label>
            <input type="number" min="0" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" required>
            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @else
        <div class="col-md-3">
            <label class="form-label">Existencia actual</label>
            <input type="text" class="form-control" value="{{ $producto->stock }}" disabled>
            <small class="text-muted">Se cambia desde
                @can('inventario')
                    <a href="{{ route('inventario.create', ['producto_id' => $producto->id]) }}">Inventario</a>.
                @else
                    Inventario.
                @endcan
            </small>
        </div>
    @endif
    <div class="col-md-3">
        <label class="form-label">Existencia mínima *</label>
        <input type="number" min="0" name="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
        @error('stock_minimo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 d-flex gap-4">
        <div class="form-check form-switch">
            <input type="checkbox" name="exento_itbis" value="1" id="exento_itbis" class="form-check-input" @checked(old('exento_itbis', $producto->exento_itbis))>
            <label for="exento_itbis" class="form-check-label">Exento de ITBIS</label>
        </div>
        @if ($producto->exists)
            <div class="form-check form-switch">
                <input type="checkbox" name="activo" value="1" id="activo" class="form-check-input" @checked(old('activo', $producto->activo))>
                <label for="activo" class="form-check-label">Producto activo</label>
            </div>
        @endif
    </div>
</div>

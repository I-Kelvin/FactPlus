@extends('layouts.app')

@section('titulo', 'Nuevo movimiento de inventario')

@section('contenido')
<div class="card" style="max-width: 700px">
    <div class="card-body">
        <form action="{{ route('inventario.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Producto *</label>
                <select name="producto_id" id="producto_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($productos as $p)
                        <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" @selected(old('producto_id', $seleccionado) == $p->id)>
                            {{ $p->codigo }} - {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Existencia actual: <strong id="stock_actual">—</strong></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de movimiento *</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="ENTRADA" @selected(old('tipo') == 'ENTRADA')>Entrada (compra / devolución)</option>
                    <option value="SALIDA" @selected(old('tipo') == 'SALIDA')>Salida (merma / daño / uso interno)</option>
                    <option value="AJUSTE" @selected(old('tipo') == 'AJUSTE')>Ajuste (conteo físico)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" id="etiqueta_cantidad">Cantidad *</label>
                <input type="number" name="cantidad" min="0" class="form-control" value="{{ old('cantidad') }}" required>
                <small class="text-muted" id="ayuda_cantidad"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Motivo / referencia *</label>
                <input type="text" name="motivo" class="form-control" value="{{ old('motivo') }}" placeholder="Ej: Compra a proveedor, factura #123" required>
            </div>

            <button class="btn btn-primary"><i class="bi bi-save"></i> Registrar</button>
            <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const selProducto = document.getElementById('producto_id');
    const selTipo = document.getElementById('tipo');

    function actualizarAyuda() {
        const opcion = selProducto.options[selProducto.selectedIndex];
        document.getElementById('stock_actual').textContent = opcion.dataset.stock ?? '—';

        if (selTipo.value === 'AJUSTE') {
            document.getElementById('etiqueta_cantidad').textContent = 'Existencia real contada *';
            document.getElementById('ayuda_cantidad').textContent = 'El sistema pondrá la existencia en este número.';
        } else {
            document.getElementById('etiqueta_cantidad').textContent = 'Cantidad *';
            document.getElementById('ayuda_cantidad').textContent = '';
        }
    }

    selProducto.addEventListener('change', actualizarAyuda);
    selTipo.addEventListener('change', actualizarAyuda);
    actualizarAyuda();
</script>
@endpush

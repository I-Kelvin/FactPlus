<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tipo de comprobante *</label>
        <select name="tipo" class="form-select" required>
            @foreach ($tipos as $codigo => $nombre)
                <option value="{{ $codigo }}" @selected(old('tipo', $secuencia->tipo) == $codigo)>{{ $codigo }} - {{ $nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Fecha de vencimiento</label>
        <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', $secuencia->fecha_vencimiento?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Último número usado *</label>
        <input type="number" min="0" name="secuencia_actual" class="form-control" value="{{ old('secuencia_actual', $secuencia->secuencia_actual ?? 0) }}" required>
        <small class="text-muted">Si es una secuencia nueva deje 0.</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Número final autorizado *</label>
        <input type="number" min="1" name="secuencia_final" class="form-control" value="{{ old('secuencia_final', $secuencia->secuencia_final) }}" required>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input type="checkbox" name="activo" value="1" id="activo" class="form-check-input" @checked(old('activo', $secuencia->exists ? $secuencia->activo : true))>
            <label for="activo" class="form-check-label">Secuencia activa</label>
        </div>
    </div>
</div>

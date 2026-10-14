<div class="mb-3">
    <label class="form-label">Nombre *</label>
    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $categoria->nombre) }}" required>
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Descripción</label>
    <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $categoria->descripcion) }}">
</div>

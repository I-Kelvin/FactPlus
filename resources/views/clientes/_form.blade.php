{{-- Formulario compartido para crear y editar clientes --}}
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nombre o razón social *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $cliente->nombre) }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipo de documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-select">
            @foreach (['CEDULA' => 'Cédula', 'RNC' => 'RNC', 'NINGUNO' => 'Ninguno'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('tipo_documento', $cliente->tipo_documento ?? 'CEDULA') == $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Cédula / RNC</label>
        <input type="text" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror" value="{{ old('documento', $cliente->documento_formateado) }}" placeholder="001-0000000-0">
        @error('documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}" placeholder="809-000-0000">
    </div>
    <div class="col-md-4">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $cliente->correo) }}">
        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}">
    </div>
    @if ($cliente->exists)
        <div class="col-12">
            <input type="hidden" name="activo" value="0">
            <div class="form-check form-switch">
                <input type="checkbox" name="activo" value="1" id="activo" class="form-check-input" @checked(old('activo', $cliente->activo))>
                <label for="activo" class="form-check-label">Cliente activo</label>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Cambia el placeholder segun el tipo de documento
    const tipoDoc = document.getElementById('tipo_documento');
    const documento = document.getElementById('documento');

    function cambiarTipo() {
        if (tipoDoc.value === 'RNC') {
            documento.placeholder = '1-01-00000-0';
            documento.disabled = false;
        } else if (tipoDoc.value === 'CEDULA') {
            documento.placeholder = '001-0000000-0';
            documento.disabled = false;
        } else {
            documento.value = '';
            documento.disabled = true;
        }
    }

    tipoDoc.addEventListener('change', cambiarTipo);
    cambiarTipo();
</script>
@endpush

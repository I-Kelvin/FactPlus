{{-- Formulario compartido de usuarios --}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre completo *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo (para iniciar sesión) *</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Contraseña {{ $usuario->exists ? '' : '*' }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $usuario->exists ? '' : 'required' }} autocomplete="new-password">
        @if ($usuario->exists)
            <small class="text-muted">Déjela en blanco para no cambiarla.</small>
        @endif
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
    </div>

    <div class="col-12 d-flex gap-4">
        <div class="form-check form-switch">
            <input type="checkbox" name="activo" value="1" id="activo" class="form-check-input" @checked(old('activo', $usuario->activo))>
            <label for="activo" class="form-check-label">Usuario activo</label>
        </div>
        <div class="form-check form-switch">
            <input type="checkbox" name="es_admin" value="1" id="es_admin" class="form-check-input" @checked(old('es_admin', $usuario->es_admin))>
            <label for="es_admin" class="form-check-label">Administrador (acceso a todo)</label>
        </div>
    </div>

    <div class="col-12" id="cajaPermisos">
        <label class="form-label">Módulos a los que tiene acceso</label>
        <div class="row">
            @foreach ($permisos as $clave => $nombre)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" name="permisos[]" value="{{ $clave }}" id="permiso_{{ $clave }}" class="form-check-input"
                               @checked(in_array($clave, old('permisos', $usuario->permisos ?? [])))>
                        <label for="permiso_{{ $clave }}" class="form-check-label">{{ $nombre }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        <small class="text-muted">El inicio y el cambio de contraseña están disponibles para todos.</small>
    </div>
</div>

@push('scripts')
<script>
    // Si es administrador no hace falta marcar los modulos
    const esAdmin = document.getElementById('es_admin');
    function revisarAdmin() {
        document.querySelectorAll('#cajaPermisos input').forEach(function (input) {
            input.disabled = esAdmin.checked;
        });
        document.getElementById('cajaPermisos').style.opacity = esAdmin.checked ? 0.5 : 1;
    }
    esAdmin.addEventListener('change', revisarAdmin);
    revisarAdmin();
</script>
@endpush

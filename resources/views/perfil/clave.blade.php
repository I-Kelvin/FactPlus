@extends('layouts.app')

@section('titulo', 'Cambiar contraseña')

@section('contenido')
<div class="card" style="max-width: 500px">
    <div class="card-body">
        <form action="{{ route('perfil.clave.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Contraseña actual *</label>
                <input type="password" name="clave_actual" class="form-control @error('clave_actual') is-invalid @enderror" required autocomplete="current-password">
                @error('clave_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva contraseña *</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                <small class="text-muted">Mínimo 8 caracteres.</small>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar nueva contraseña *</label>
                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
            <button class="btn btn-primary"><i class="bi bi-key"></i> Cambiar contraseña</button>
        </form>
    </div>
</div>
@endsection

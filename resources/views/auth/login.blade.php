<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Fact+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
</head>
<body>
<div class="pagina-login d-flex align-items-center justify-content-center p-3">
    <div class="card p-4 shadow">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff text-warning"></i> Fact<span class="text-warning">+</span></h2>
            <small class="text-muted">Sistema de Facturación e Inventario</small>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="recordar" id="recordar" class="form-check-input">
                <label for="recordar" class="form-check-label">Recordarme</label>
            </div>
            <button class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</div>
</body>
</html>

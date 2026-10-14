<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') | Fact+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
    @stack('estilos')
</head>
<body>

<div class="d-flex">
    {{-- Menu lateral --}}
    <nav id="sidebar" class="sidebar d-flex flex-column p-3">
        <a href="{{ route('dashboard') }}" class="marca mb-4 text-decoration-none">
            <i class="bi bi-receipt-cutoff"></i> Fact<span>+</span>
        </a>

        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Inicio</a></li>

            {{-- Cada opcion solo se muestra si el usuario tiene ese permiso --}}
            @canany(['facturas', 'clientes', 'ncf'])
                <li class="titulo-menu">Facturación</li>
            @endcanany
            @can('facturas')
                <li><a href="{{ route('facturas.create') }}" class="nav-link {{ request()->routeIs('facturas.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Nueva factura</a></li>
                <li><a href="{{ route('facturas.index') }}" class="nav-link {{ request()->routeIs('facturas.index', 'facturas.show') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Facturas</a></li>
            @endcan
            @can('clientes')
                <li><a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Clientes</a></li>
            @endcan
            @can('ncf')
                <li><a href="{{ route('ncf.index') }}" class="nav-link {{ request()->routeIs('ncf.*') ? 'active' : '' }}"><i class="bi bi-upc-scan"></i> Comprobantes NCF</a></li>
            @endcan

            @canany(['productos', 'inventario'])
                <li class="titulo-menu">Inventario</li>
            @endcanany
            @can('productos')
                <li><a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Productos</a></li>
                <li><a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Categorías</a></li>
            @endcan
            @can('inventario')
                <li><a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Movimientos</a></li>
            @endcan

            @can('reportes')
                <li class="titulo-menu">Reportes</li>
                <li><a href="{{ route('reportes.ventas') }}" class="nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}"><i class="bi bi-graph-up"></i> Ventas</a></li>
                <li><a href="{{ route('reportes.inventario') }}" class="nav-link {{ request()->routeIs('reportes.inventario') ? 'active' : '' }}"><i class="bi bi-clipboard-data"></i> Inventario</a></li>
            @endcan

            @canany(['empresa', 'usuarios'])
                <li class="titulo-menu">Sistema</li>
            @endcanany
            @can('empresa')
                <li><a href="{{ route('empresa.edit') }}" class="nav-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}"><i class="bi bi-building-gear"></i> Empresa</a></li>
            @endcan
            @can('usuarios')
                <li><a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> Usuarios</a></li>
            @endcan
        </ul>
    </nav>

    <div class="contenido flex-grow-1">
        {{-- Barra superior --}}
        <header class="barra-superior d-flex justify-content-between align-items-center px-4 py-2">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('abierto')">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 d-none d-lg-block">@yield('titulo', 'Inicio')</h5>
            <div class="dropdown">
                <a href="#" class="text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">{{ auth()->user()->es_admin ? 'Administrador' : 'Usuario' }}</h6></li>
                    <li><a class="dropdown-item" href="{{ route('perfil.clave') }}"><i class="bi bi-key"></i> Cambiar contraseña</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="p-4">
            {{-- Mensajes de exito / advertencia --}}
            @foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $clave => $color)
                @if (session($clave))
                    <div class="alert alert-{{ $color }} alert-dismissible fade show">
                        {{ session($clave) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Revise los datos:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('contenido')
        </main>

        <footer class="text-center text-muted small py-3">
            Fact+ &middot; Sistema de Facturación e Inventario &middot; {{ date('Y') }}
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Pide confirmacion antes de eliminar
    document.querySelectorAll('form.confirmar').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.dataset.mensaje || '¿Seguro que desea eliminar este registro?')) {
                e.preventDefault();
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>

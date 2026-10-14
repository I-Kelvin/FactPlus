<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\NcfController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas para usuarios sin sesion
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Todo lo demas requiere haber iniciado sesion
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // El inicio y el cambio de contraseña los puede usar cualquier usuario
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil/clave', [PerfilController::class, 'editarClave'])->name('perfil.clave');
    Route::put('/perfil/clave', [PerfilController::class, 'actualizarClave'])->name('perfil.clave.update');

    // Cada grupo de rutas pide el permiso del modulo (ver AppServiceProvider)
    Route::middleware('can:clientes')->group(function () {
        Route::resource('clientes', ClienteController::class);
    });

    Route::middleware('can:productos')->group(function () {
        Route::resource('categorias', CategoriaController::class)->except('show');
        Route::resource('productos', ProductoController::class);
    });

    // Inventario (kardex, entradas, salidas y ajustes)
    Route::middleware('can:inventario')->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/nuevo', [InventarioController::class, 'create'])->name('inventario.create');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
    });

    // Comprobantes fiscales
    Route::middleware('can:ncf')->group(function () {
        Route::resource('ncf', NcfController::class)->except('show', 'destroy');
    });

    // Facturacion
    Route::middleware('can:facturas')->group(function () {
        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
        Route::get('/facturas/nueva', [FacturaController::class, 'create'])->name('facturas.create');
        Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
        Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
        Route::get('/facturas/{factura}/imprimir', [FacturaController::class, 'imprimir'])->name('facturas.imprimir');
    });
    Route::patch('/facturas/{factura}/anular', [FacturaController::class, 'anular'])
        ->name('facturas.anular')
        ->middleware('can:anular_facturas');

    // Reportes
    Route::middleware('can:reportes')->group(function () {
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    });

    // Configuracion de la empresa
    Route::middleware('can:empresa')->group(function () {
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
    });

    // Usuarios y permisos
    Route::middleware('can:usuarios')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except('show');
    });
});

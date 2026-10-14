<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginacion con estilos de Bootstrap 5
        Paginator::useBootstrapFive();

        // Fechas en español (ej: "hace 2 horas", "octubre")
        Carbon::setLocale('es');

        // Un permiso (Gate) por cada modulo. Asi se puede usar
        // @can('clientes') en las vistas y ->middleware('can:clientes') en las rutas
        foreach (array_keys(User::PERMISOS) as $permiso) {
            Gate::define($permiso, function (User $user) use ($permiso) {
                return $user->puede($permiso);
            });
        }
    }
}

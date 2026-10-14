<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si desactivan a un usuario mientras tiene la sesion abierta,
 * en la siguiente pagina que visite se le cierra la sesion.
 */
class UsuarioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Su usuario está desactivado.']);
        }

        return $next($request);
    }
}

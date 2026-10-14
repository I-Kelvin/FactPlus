<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    // Cada usuario puede cambiar su propia contraseña
    public function editarClave()
    {
        return view('perfil.clave');
    }

    public function actualizarClave(Request $request)
    {
        $request->validate([
            'clave_actual' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed|different:clave_actual',
        ]);

        $request->user()->update(['password' => $request->password]);

        return redirect()->route('dashboard')->with('success', 'Su contraseña fue cambiada.');
    }
}

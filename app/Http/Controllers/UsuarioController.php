<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create', ['usuario' => new User(['activo' => true]), 'permisos' => User::PERMISOS]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'permisos' => 'nullable|array',
            'permisos.*' => Rule::in(array_keys(User::PERMISOS)),
        ]);

        $datos['es_admin'] = $request->boolean('es_admin');
        $datos['activo'] = $request->boolean('activo');
        $datos['permisos'] = $datos['permisos'] ?? [];

        User::create($datos);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', ['usuario' => $usuario, 'permisos' => User::PERMISOS]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($usuario->id)],
            // Si se deja vacia se queda la contraseña que tenia
            'password' => 'nullable|string|min:8|confirmed',
            'permisos' => 'nullable|array',
            'permisos.*' => Rule::in(array_keys(User::PERMISOS)),
        ]);

        $datos['es_admin'] = $request->boolean('es_admin');
        $datos['activo'] = $request->boolean('activo');
        $datos['permisos'] = $datos['permisos'] ?? [];

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        // Para que nadie se quede fuera del sistema por error
        if ($usuario->id == auth()->id()) {
            if (! $datos['activo']) {
                throw ValidationException::withMessages(['activo' => 'No puede desactivar su propio usuario.']);
            }
            if (! $datos['es_admin'] && ! in_array('usuarios', $datos['permisos']) && $usuario->puede('usuarios')) {
                throw ValidationException::withMessages(['permisos' => 'No puede quitarse a sí mismo el acceso a Usuarios.']);
            }
        }

        if ($usuario->es_admin && (! $datos['es_admin'] || ! $datos['activo']) && $this->esUltimoAdmin($usuario)) {
            throw ValidationException::withMessages(['es_admin' => 'Debe quedar al menos un administrador activo.']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id == auth()->id()) {
            return back()->with('error', 'No puede eliminar su propio usuario.');
        }

        if ($usuario->es_admin && $this->esUltimoAdmin($usuario)) {
            return back()->with('error', 'Debe quedar al menos un administrador activo.');
        }

        // Si ya facturo se desactiva para no perder el historial
        if ($usuario->facturas()->exists()) {
            $usuario->update(['activo' => false]);

            return back()->with('warning', 'El usuario tiene facturas registradas, por eso solo se desactivó.');
        }

        $usuario->delete();

        return back()->with('success', 'Usuario eliminado.');
    }

    private function esUltimoAdmin(User $usuario)
    {
        return User::where('es_admin', true)
            ->where('activo', true)
            ->where('id', '!=', $usuario->id)
            ->doesntExist();
    }
}

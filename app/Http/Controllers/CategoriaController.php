<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->paginate(10);

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create', ['categoria' => new Categoria()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:80|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Categoria::create($datos);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80', Rule::unique('categorias', 'nombre')->ignore($categoria->id)],
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria->update($datos);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        // Los productos quedan sin categoria (nullOnDelete en la migracion)
        $categoria->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}

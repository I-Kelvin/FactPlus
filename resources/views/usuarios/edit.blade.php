@extends('layouts.app')

@section('titulo', 'Editar usuario')

@section('contenido')
<div class="card" style="max-width: 900px">
    <div class="card-body">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')
            @include('usuarios._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('titulo', 'Editar categoría')

@section('contenido')
<div class="card" style="max-width: 600px">
    <div class="card-body">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')
            @include('categorias._form')
            <button class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

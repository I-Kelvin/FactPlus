@extends('layouts.app')

@section('titulo', 'Editar producto')

@section('contenido')
<div class="card">
    <div class="card-body">
        <form action="{{ route('productos.update', $producto) }}" method="POST">
            @csrf
            @method('PUT')
            @include('productos._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

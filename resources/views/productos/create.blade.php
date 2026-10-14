@extends('layouts.app')

@section('titulo', 'Nuevo producto')

@section('contenido')
<div class="card">
    <div class="card-body">
        <form action="{{ route('productos.store') }}" method="POST">
            @csrf
            @include('productos._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

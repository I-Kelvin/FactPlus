@extends('layouts.app')

@section('titulo', 'Nuevo usuario')

@section('contenido')
<div class="card" style="max-width: 900px">
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            @include('usuarios._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

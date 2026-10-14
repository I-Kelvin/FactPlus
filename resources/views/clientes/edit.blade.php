@extends('layouts.app')

@section('titulo', 'Editar cliente')

@section('contenido')
<div class="card">
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf
            @method('PUT')
            @include('clientes._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

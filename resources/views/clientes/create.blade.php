@extends('layouts.app')

@section('titulo', 'Nuevo cliente')

@section('contenido')
<div class="card">
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            @include('clientes._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

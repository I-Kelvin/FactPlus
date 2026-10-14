@extends('layouts.app')

@section('titulo', 'Editar secuencia NCF')

@section('contenido')
<div class="card" style="max-width: 700px">
    <div class="card-body">
        <form action="{{ route('ncf.update', $secuencia) }}" method="POST">
            @csrf
            @method('PUT')
            @include('ncf._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                <a href="{{ route('ncf.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

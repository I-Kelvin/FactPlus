@extends('layouts.app')

@section('titulo', 'Nueva secuencia NCF')

@section('contenido')
<div class="card" style="max-width: 700px">
    <div class="card-body">
        <form action="{{ route('ncf.store') }}" method="POST">
            @csrf
            @include('ncf._form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                <a href="{{ route('ncf.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('titulo', 'Acceso denegado')

@section('contenido')
<div class="card text-center py-5">
    <div class="card-body">
        <i class="bi bi-shield-lock text-danger" style="font-size: 4rem"></i>
        <h3 class="mt-3">No tiene permiso para entrar aquí</h3>
        <p class="text-muted">Si necesita acceso a este módulo, pídale al administrador que se lo asigne en Usuarios.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="bi bi-house"></i> Volver al inicio</a>
    </div>
</div>
@endsection

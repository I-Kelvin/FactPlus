@extends('layouts.app')

@section('titulo', 'Datos de la empresa')

@section('contenido')
<div class="card" style="max-width: 800px">
    <div class="card-header">Esta información aparece en el encabezado de las facturas</div>
    <div class="card-body">
        <form action="{{ route('empresa.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre / razón social *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $empresa->nombre) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">RNC</label>
                    <input type="text" name="rnc" class="form-control" value="{{ old('rnc', $empresa->rnc) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $empresa->direccion) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $empresa->telefono) }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo', $empresa->correo) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ITBIS (%) *</label>
                    <input type="number" step="0.01" min="0" max="100" name="itbis" class="form-control" value="{{ old('itbis', $empresa->itbis) }}" required>
                </div>
            </div>
            <button class="btn btn-primary mt-4"><i class="bi bi-save"></i> Guardar</button>
        </form>
    </div>
</div>
@endsection

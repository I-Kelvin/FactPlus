@extends('layouts.app')

@section('titulo', 'Nueva factura')

@section('contenido')
<form action="{{ route('facturas.store') }}" method="POST" id="formFactura">
    @csrf
    <div class="row g-3">
        {{-- Datos generales --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Datos de la factura</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="form-select">
                            <option value="" data-doc="">Consumidor final (sin cliente)</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" data-doc="{{ $cliente->documento }}" @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->nombre }} {{ $cliente->documento ? '(' . $cliente->documento_formateado . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @can('clientes')
                            <small><a href="{{ route('clientes.create') }}" target="_blank">+ Registrar cliente nuevo</a></small>
                        @endcan
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de comprobante *</label>
                        <select name="tipo_ncf" id="tipo_ncf" class="form-select" required>
                            @foreach ($secuencias as $s)
                                <option value="{{ $s->tipo }}" @selected(old('tipo_ncf', 'B02') == $s->tipo) @disabled($s->vencida || $s->disponibles <= 0)>
                                    {{ $s->tipo }} - {{ $s->descripcion }} ({{ $s->disponibles }} disp.)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger d-none" id="avisoNcf">El crédito fiscal requiere un cliente con RNC o cédula.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Fecha *</label>
                            <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Condición *</label>
                            <select name="condicion" class="form-select">
                                <option value="CONTADO" @selected(old('condicion') == 'CONTADO')>Contado</option>
                                <option value="CREDITO" @selected(old('condicion') == 'CREDITO')>Crédito</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descuento (%)</label>
                        <input type="number" name="descuento_porcentaje" id="descuento" class="form-control" min="0" max="100" step="0.01" value="{{ old('descuento_porcentaje', 0) }}">
                    </div>

                    <div>
                        <label class="form-label">Nota</label>
                        <input type="text" name="nota" class="form-control" value="{{ old('nota') }}" maxlength="255">
                    </div>
                </div>
            </div>
        </div>

        {{-- Detalle de productos --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Productos</div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <select id="selProducto" class="form-select">
                                <option value="">-- Buscar producto --</option>
                                @foreach ($productos as $p)
                                    <option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nombre }} ({{ dinero($p->precio_venta) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="cantProducto" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success w-100" onclick="agregarProducto()"><i class="bi bi-plus-lg"></i> Agregar</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm tabla-factura align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Precio</th>
                                    <th style="width: 100px">Cant.</th>
                                    <th class="text-end">ITBIS</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                                <tr id="filaVacia"><td colspan="6" class="text-center text-muted">Agregue productos a la factura.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        <table class="totales-factura">
                            <tr><td>Subtotal:</td><td class="text-end" id="tSubtotal">RD$ 0.00</td></tr>
                            <tr><td>Descuento:</td><td class="text-end text-danger" id="tDescuento">RD$ 0.00</td></tr>
                            <tr><td>ITBIS ({{ (float) $empresa->itbis }}%):</td><td class="text-end" id="tItbis">RD$ 0.00</td></tr>
                            <tr class="fs-5 fw-bold"><td>Total:</td><td class="text-end" id="tTotal">RD$ 0.00</td></tr>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary" id="btnGuardar"><i class="bi bi-check-lg"></i> Guardar factura</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Lista de productos que se pueden facturar
    const productos = {{ Js::from($listaProductos) }};

    const tasaItbis = {{ (float) $empresa->itbis }};
    let lineas = []; // cada linea: { id, cantidad }

    // Para que los nombres con < o & no rompan la tabla
    function escapar(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function formato(valor) {
        return 'RD$ ' + valor.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function agregarProducto() {
        const id = document.getElementById('selProducto').value;
        const cantidad = parseInt(document.getElementById('cantProducto').value) || 0;

        if (!id) {
            alert('Seleccione un producto.');
            return;
        }
        if (cantidad < 1) {
            alert('La cantidad debe ser mayor que cero.');
            return;
        }

        // Si ya esta en la factura se suma la cantidad
        const existente = lineas.find(l => l.id == id);
        const nuevaCantidad = (existente ? existente.cantidad : 0) + cantidad;

        if (nuevaCantidad > productos[id].stock) {
            alert('Solo hay ' + productos[id].stock + ' unidades de ' + productos[id].nombre + '.');
            return;
        }

        if (existente) {
            existente.cantidad = nuevaCantidad;
        } else {
            lineas.push({ id: id, cantidad: cantidad });
        }

        document.getElementById('selProducto').value = '';
        document.getElementById('cantProducto').value = 1;
        dibujar();
    }

    function cambiarCantidad(indice, valor) {
        let cant = parseInt(valor) || 1;
        const prod = productos[lineas[indice].id];
        if (cant > prod.stock) {
            alert('Solo hay ' + prod.stock + ' unidades disponibles.');
            cant = prod.stock;
        }
        lineas[indice].cantidad = Math.max(cant, 1);
        dibujar();
    }

    function quitar(indice) {
        lineas.splice(indice, 1);
        dibujar();
    }

    // Vuelve a pintar la tabla y calcula los totales
    function dibujar() {
        const tbody = document.getElementById('detalle');
        const pctDesc = parseFloat(document.getElementById('descuento').value) || 0;
        let subtotal = 0, descuento = 0, itbis = 0;
        let html = '';

        lineas.forEach(function (linea, i) {
            const p = productos[linea.id];
            const importe = p.precio * linea.cantidad;
            const desc = importe * pctDesc / 100;
            const imp = p.exento ? 0 : (importe - desc) * tasaItbis / 100;

            subtotal += importe;
            descuento += desc;
            itbis += imp;

            html += '<tr>' +
                '<td>' + escapar(p.nombre) + (p.exento ? ' <span class="badge bg-light text-dark">Exento</span>' : '') +
                '<input type="hidden" name="productos[' + i + '][id]" value="' + linea.id + '"></td>' +
                '<td class="text-end">' + formato(p.precio) + '</td>' +
                '<td><input type="number" class="form-control form-control-sm" name="productos[' + i + '][cantidad]" value="' + linea.cantidad + '" min="1" max="' + p.stock + '" onchange="cambiarCantidad(' + i + ', this.value)"></td>' +
                '<td class="text-end">' + formato(imp) + '</td>' +
                '<td class="text-end">' + formato(importe - desc + imp) + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger" onclick="quitar(' + i + ')"><i class="bi bi-x"></i></button></td>' +
                '</tr>';
        });

        tbody.innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">Agregue productos a la factura.</td></tr>';

        document.getElementById('tSubtotal').textContent = formato(subtotal);
        document.getElementById('tDescuento').textContent = '- ' + formato(descuento);
        document.getElementById('tItbis').textContent = formato(itbis);
        document.getElementById('tTotal').textContent = formato(subtotal - descuento + itbis);
    }

    // Aviso si elige B01 sin un cliente con documento
    function revisarNcf() {
        const cliente = document.getElementById('cliente_id');
        const doc = cliente.options[cliente.selectedIndex].dataset.doc;
        const tipo = document.getElementById('tipo_ncf').value;
        document.getElementById('avisoNcf').classList.toggle('d-none', tipo === 'B02' || doc !== '');
    }

    document.getElementById('descuento').addEventListener('input', dibujar);
    document.getElementById('cliente_id').addEventListener('change', revisarNcf);
    document.getElementById('tipo_ncf').addEventListener('change', revisarNcf);

    document.getElementById('formFactura').addEventListener('submit', function (e) {
        if (lineas.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto.');
            return;
        }
        // Evita que se guarde dos veces por doble clic
        document.getElementById('btnGuardar').disabled = true;
    });

    // Si hubo un error de validacion se recuperan los productos que habia
    const anteriores = {{ Js::from(old('productos', [])) }};
    Object.values(anteriores).forEach(function (l) {
        if (productos[l.id]) {
            lineas.push({ id: l.id, cantidad: parseInt(l.cantidad) || 1 });
        }
    });
    dibujar();
    revisarNcf();
</script>
@endpush

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->ncf }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; }
        .hoja { max-width: 780px; margin: 20px auto; padding: 25px; border: 1px solid #ddd; }
        .encabezado { display: flex; justify-content: space-between; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
        .encabezado h2 { margin: 0; color: #1e3a5f; }
        .caja-ncf { border: 1px solid #1e3a5f; padding: 8px 12px; text-align: right; }
        .caja-ncf h3 { margin: 0 0 4px; font-size: 14px; }
        .cliente { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; }
        .detalle th { background: #1e3a5f; color: #fff; padding: 6px; text-align: left; }
        .detalle td { padding: 6px; border-bottom: 1px solid #eee; }
        .der { text-align: right !important; }
        .centro { text-align: center !important; }
        .totales { width: 280px; margin-left: auto; margin-top: 10px; }
        .totales td { padding: 4px 6px; }
        .totales .total { font-size: 15px; font-weight: bold; border-top: 2px solid #1e3a5f; }
        .anulada { color: #c00; font-size: 28px; font-weight: bold; text-align: center; border: 3px solid #c00; padding: 6px; margin: 10px 0; }
        .pie { margin-top: 50px; display: flex; justify-content: space-around; text-align: center; }
        .pie div { border-top: 1px solid #555; width: 220px; padding-top: 4px; }
        .botones { text-align: center; margin: 15px; }
        @media print {
            .botones { display: none; }
            .hoja { border: none; margin: 0; }
        }
    </style>
</head>
<body>
<div class="botones">
    <button onclick="window.print()">Imprimir</button>
    <button onclick="window.close()">Cerrar</button>
</div>

<div class="hoja">
    <div class="encabezado">
        <div>
            <h2>{{ $empresa->nombre }}</h2>
            @if ($empresa->rnc) <div><strong>RNC:</strong> {{ $empresa->rnc }}</div> @endif
            <div>{{ $empresa->direccion }}</div>
            <div>Tel.: {{ $empresa->telefono }} {{ $empresa->correo ? '| ' . $empresa->correo : '' }}</div>
        </div>
        <div class="caja-ncf">
            <h3>{{ $factura->tipo_ncf == 'B01' ? 'FACTURA DE CRÉDITO FISCAL' : 'FACTURA DE CONSUMO' }}</h3>
            <div><strong>NCF:</strong> {{ $factura->ncf }}</div>
            <div><strong>No.:</strong> {{ $factura->numero }}</div>
            <div><strong>Fecha:</strong> {{ $factura->fecha->format('d/m/Y') }}</div>
            <div><strong>Condición:</strong> {{ ucfirst(strtolower($factura->condicion)) }}</div>
        </div>
    </div>

    @if ($factura->anulada)
        <div class="anulada">ANULADA</div>
    @endif

    <div class="cliente">
        <strong>Cliente:</strong> {{ $factura->cliente->nombre ?? 'Consumidor final' }}<br>
        @if ($factura->cliente && $factura->cliente->documento)
            <strong>{{ $factura->cliente->tipo_documento == 'RNC' ? 'RNC' : 'Cédula' }}:</strong> {{ $factura->cliente->documento_formateado }}<br>
        @endif
        @if ($factura->cliente && $factura->cliente->direccion)
            <strong>Dirección:</strong> {{ $factura->cliente->direccion }}
        @endif
    </div>

    <table class="detalle">
        <thead>
            <tr>
                <th class="centro">Cant.</th>
                <th>Descripción</th>
                <th class="der">Precio</th>
                <th class="der">ITBIS</th>
                <th class="der">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($factura->detalles as $d)
                <tr>
                    <td class="centro">{{ $d->cantidad }}</td>
                    <td>{{ $d->descripcion }}{{ $d->itbis == 0 ? ' (E)' : '' }}</td>
                    <td class="der">{{ number_format($d->precio, 2) }}</td>
                    <td class="der">{{ number_format($d->itbis, 2) }}</td>
                    <td class="der">{{ number_format($d->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr><td>Subtotal:</td><td class="der">{{ dinero($factura->subtotal) }}</td></tr>
        @if ($factura->descuento > 0)
            <tr><td>Descuento:</td><td class="der">- {{ dinero($factura->descuento) }}</td></tr>
        @endif
        <tr><td>ITBIS:</td><td class="der">{{ dinero($factura->itbis) }}</td></tr>
        <tr class="total"><td>TOTAL:</td><td class="der">{{ dinero($factura->total) }}</td></tr>
    </table>

    <p style="font-size: 10px; color: #777;">(E) = Exento de ITBIS</p>

    <div class="pie">
        <div>Despachado por</div>
        <div>Recibido conforme</div>
    </div>
</div>
</body>
</html>

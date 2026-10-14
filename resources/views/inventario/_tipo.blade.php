@php
    $colores = ['ENTRADA' => 'success', 'SALIDA' => 'danger', 'AJUSTE' => 'warning'];
@endphp
<span class="badge bg-{{ $colores[$mov->tipo] ?? 'secondary' }}">{{ ucfirst(strtolower($mov->tipo)) }}</span>

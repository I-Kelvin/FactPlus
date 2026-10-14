<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $ventasHoy = Factura::emitidas()->whereDate('fecha', $hoy)->sum('total');
        $ventasMes = Factura::emitidas()
            ->whereBetween('fecha', [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()])
            ->sum('total');
        $facturasMes = Factura::emitidas()
            ->whereBetween('fecha', [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()])
            ->count();

        $totalProductos = Producto::where('activo', true)->count();
        $totalClientes = Cliente::where('activo', true)->count();

        $stockBajo = Producto::where('activo', true)->stockBajo()->orderBy('stock')->take(8)->get();
        $ultimasFacturas = Factura::with('cliente')->latest()->take(6)->get();

        // Ventas de los ultimos 7 dias para la grafica
        $dias = [];
        $montos = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = $hoy->copy()->subDays($i);
            $dias[] = $dia->translatedFormat('D d');
            $montos[] = (float) Factura::emitidas()->whereDate('fecha', $dia)->sum('total');
        }

        return view('dashboard', compact(
            'ventasHoy', 'ventasMes', 'facturasMes', 'totalProductos', 'totalClientes',
            'stockBajo', 'ultimasFacturas', 'dias', 'montos'
        ));
    }
}

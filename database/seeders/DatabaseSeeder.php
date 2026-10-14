<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\SecuenciaNcf;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos iniciales para poder probar el sistema.
     * Se usa firstOrCreate para que si se vuelve a ejecutar no borre
     * lo que ya se cambio (por ejemplo, el NCF que va por la secuencia).
     */
    public function run(): void
    {
        // Usuario administrador (cambiar la clave despues de instalar)
        $admin = User::firstOrCreate(
            ['email' => 'admin@factplus.do'],
            ['name' => 'Administrador', 'password' => env('ADMIN_PASSWORD', 'admin123')]
        );
        $admin->update(['es_admin' => true, 'activo' => true]);

        // Usuarios de ejemplo con acceso limitado
        User::firstOrCreate(['email' => 'cajero@factplus.do'], [
            'name' => 'María Cajera',
            'password' => env('DEMO_PASSWORD', 'demo1234'),
            'permisos' => ['facturas', 'clientes'],
        ]);
        User::firstOrCreate(['email' => 'almacen@factplus.do'], [
            'name' => 'Pedro Almacén',
            'password' => env('DEMO_PASSWORD', 'demo1234'),
            'permisos' => ['productos', 'inventario', 'reportes'],
        ]);

        Empresa::firstOrCreate(['id' => 1], [
            'nombre' => 'Colmado y Ferretería El Progreso SRL',
            'rnc' => '130123454',
            'direccion' => 'C/ Duarte #45, Santiago de los Caballeros',
            'telefono' => '809-555-1234',
            'correo' => 'ventas@elprogreso.do',
            'itbis' => 18,
        ]);

        // Secuencias de comprobantes
        $vence = Carbon::create(2027, 12, 31);
        SecuenciaNcf::firstOrCreate(['tipo' => 'B01'], [
            'descripcion' => 'Crédito Fiscal', 'secuencia_actual' => 0, 'secuencia_final' => 500,
            'fecha_vencimiento' => $vence, 'activo' => true,
        ]);
        SecuenciaNcf::firstOrCreate(['tipo' => 'B02'], [
            'descripcion' => 'Consumidor Final', 'secuencia_actual' => 0, 'secuencia_final' => 2000,
            'fecha_vencimiento' => $vence, 'activo' => true,
        ]);

        // Categorias
        $categorias = [];
        foreach (['Alimentos', 'Bebidas', 'Limpieza', 'Ferretería', 'Electricidad'] as $nombre) {
            $categorias[$nombre] = Categoria::firstOrCreate(['nombre' => $nombre])->id;
        }

        // Productos: codigo, nombre, categoria, costo, venta, stock, minimo, exento
        $productos = [
            ['ALI-001', 'Arroz Selecto 5 lb', 'Alimentos', 160, 195, 60, 10, true],
            ['ALI-002', 'Habichuelas Rojas 1 lb', 'Alimentos', 65, 85, 40, 10, true],
            ['ALI-003', 'Aceite de Soya 1 L', 'Alimentos', 180, 225, 25, 8, false],
            ['ALI-004', 'Azúcar Crema 2 lb', 'Alimentos', 55, 70, 30, 10, true],
            ['ALI-005', 'Salami Súper Especial 1 lb', 'Alimentos', 140, 180, 4, 6, false],
            ['ALI-006', 'Huevos (cartón de 30)', 'Alimentos', 210, 260, 15, 5, true],
            ['BEB-001', 'Refresco Rojo 2 L', 'Bebidas', 75, 100, 48, 12, false],
            ['BEB-002', 'Agua Botellón 5 Gal', 'Bebidas', 60, 85, 20, 5, false],
            ['BEB-003', 'Malta 12 oz', 'Bebidas', 35, 50, 3, 12, false],
            ['LIM-001', 'Detergente en Polvo 1 kg', 'Limpieza', 120, 155, 18, 6, false],
            ['LIM-002', 'Cloro 1 Galón', 'Limpieza', 85, 110, 22, 6, false],
            ['FER-001', 'Cemento Gris 42.5 kg', 'Ferretería', 520, 620, 35, 10, false],
            ['FER-002', 'Varilla 3/8" (unidad)', 'Ferretería', 190, 245, 80, 20, false],
            ['FER-003', 'Pintura Blanca Galón', 'Ferretería', 780, 950, 9, 4, false],
            ['ELE-001', 'Bombillo LED 9W', 'Electricidad', 55, 85, 50, 10, false],
            ['ELE-002', 'Cable THHN #12 (pie)', 'Electricidad', 18, 28, 300, 50, false],
        ];

        foreach ($productos as $p) {
            if (Producto::where('codigo', $p[0])->exists()) {
                continue;
            }

            $producto = Producto::create([
                'codigo' => $p[0],
                'nombre' => $p[1],
                'categoria_id' => $categorias[$p[2]],
                'precio_costo' => $p[3],
                'precio_venta' => $p[4],
                'stock' => 0,
                'stock_minimo' => $p[6],
                'exento_itbis' => $p[7],
            ]);

            MovimientoInventario::registrar($producto, 'ENTRADA', $p[5], 'Inventario inicial');
        }

        // Clientes de prueba (cedulas y RNC ficticios con digito verificador valido)
        $clientes = [
            ['Juan Pérez Rodríguez', 'CEDULA', '00112345673', '809-555-0101', 'Santiago'],
            ['María Altagracia Gómez', 'CEDULA', '40223456787', '829-555-0202', 'La Vega'],
            ['Constructora Cibao SRL', 'RNC', '101456787', '809-555-0303', 'Av. 27 de Febrero, Santiago'],
            ['Supermercado La Económica', 'RNC', '131987656', '849-555-0404', 'Moca, Espaillat'],
            ['Pedro Antonio Almonte', 'CEDULA', '03109876544', '809-555-0505', 'Puerto Plata'],
        ];

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(['documento' => $c[2]], [
                'nombre' => $c[0],
                'tipo_documento' => $c[1],
                'telefono' => $c[3],
                'direccion' => $c[4],
            ]);
        }

        Cliente::firstOrCreate(['nombre' => 'Cliente Contado'], ['tipo_documento' => 'NINGUNO']);
    }
}

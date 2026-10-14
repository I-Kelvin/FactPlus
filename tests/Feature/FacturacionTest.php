<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturacionTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function usuario()
    {
        return User::where('email', 'admin@factplus.do')->first();
    }

    public function test_crea_factura_consumidor_final_con_itbis_y_descuenta_stock(): void
    {
        $arroz = Producto::where('codigo', 'ALI-001')->first();   // exento, 195.00
        $cemento = Producto::where('codigo', 'FER-001')->first(); // gravado, 620.00
        $stockArroz = $arroz->stock;
        $stockCemento = $cemento->stock;

        $respuesta = $this->actingAs($this->usuario())->post('/facturas', [
            'tipo_ncf' => 'B02',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [
                ['id' => $arroz->id, 'cantidad' => 2],
                ['id' => $cemento->id, 'cantidad' => 3],
            ],
        ]);

        $factura = Factura::first();
        $respuesta->assertRedirect(route('facturas.show', $factura));

        $this->assertEquals('B0200000001', $factura->ncf);
        // 2 x 195 + 3 x 620 = 2250, ITBIS solo al cemento: 1860 x 18% = 334.80
        $this->assertEquals(2250.00, (float) $factura->subtotal);
        $this->assertEquals(334.80, (float) $factura->itbis);
        $this->assertEquals(2584.80, (float) $factura->total);

        $this->assertEquals($stockArroz - 2, $arroz->fresh()->stock);
        $this->assertEquals($stockCemento - 3, $cemento->fresh()->stock);
    }

    public function test_el_ncf_es_consecutivo(): void
    {
        $producto = Producto::where('codigo', 'BEB-001')->first();
        $datos = [
            'tipo_ncf' => 'B02',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [['id' => $producto->id, 'cantidad' => 1]],
        ];

        $this->actingAs($this->usuario())->post('/facturas', $datos);
        $this->actingAs($this->usuario())->post('/facturas', $datos);

        $this->assertEquals(['B0200000001', 'B0200000002'], Factura::orderBy('id')->pluck('ncf')->all());
    }

    public function test_credito_fiscal_requiere_cliente_con_rnc(): void
    {
        $producto = Producto::where('codigo', 'BEB-001')->first();

        $this->actingAs($this->usuario())->post('/facturas', [
            'tipo_ncf' => 'B01',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [['id' => $producto->id, 'cantidad' => 1]],
        ])->assertSessionHasErrors('cliente_id');

        $empresa = Cliente::where('tipo_documento', 'RNC')->first();

        $this->actingAs($this->usuario())->post('/facturas', [
            'cliente_id' => $empresa->id,
            'tipo_ncf' => 'B01',
            'fecha' => now()->toDateString(),
            'condicion' => 'CREDITO',
            'productos' => [['id' => $producto->id, 'cantidad' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertEquals('B0100000001', Factura::first()->ncf);
    }

    public function test_no_permite_vender_mas_de_lo_que_hay(): void
    {
        $producto = Producto::where('codigo', 'BEB-003')->first(); // malta, poco stock

        $this->actingAs($this->usuario())->post('/facturas', [
            'tipo_ncf' => 'B02',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [['id' => $producto->id, 'cantidad' => $producto->stock + 1]],
        ])->assertSessionHasErrors('productos');

        $this->assertEquals(0, Factura::count());
    }

    public function test_anular_devuelve_el_inventario(): void
    {
        $producto = Producto::where('codigo', 'ELE-001')->first();
        $stockInicial = $producto->stock;

        $this->actingAs($this->usuario())->post('/facturas', [
            'tipo_ncf' => 'B02',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [['id' => $producto->id, 'cantidad' => 5]],
        ]);

        $factura = Factura::first();
        $this->get("/facturas/{$factura->id}")->assertOk()->assertSee($factura->ncf);
        $this->get("/facturas/{$factura->id}/imprimir")->assertOk()->assertSee('FACTURA DE CONSUMO');
        $this->get("/productos/{$producto->id}")->assertOk();

        $this->actingAs($this->usuario())
            ->patch("/facturas/{$factura->id}/anular", ['motivo' => 'Error en el pedido'])
            ->assertSessionHas('success');

        $this->assertEquals('ANULADA', $factura->fresh()->estado);
        $this->assertEquals($stockInicial, $producto->fresh()->stock);
    }

    public function test_las_paginas_principales_cargan(): void
    {
        $this->actingAs($this->usuario());

        foreach (['/', '/clientes', '/productos', '/categorias', '/inventario', '/ncf', '/facturas', '/facturas/nueva', '/reportes/ventas', '/reportes/inventario', '/empresa', '/clientes/create', '/productos/create', '/inventario/nuevo'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}

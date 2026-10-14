<?php

namespace Tests\Feature;

use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin()
    {
        return User::where('email', 'admin@factplus.do')->first();
    }

    private function cajero()
    {
        return User::where('email', 'cajero@factplus.do')->first();
    }

    public function test_el_cajero_solo_entra_a_sus_modulos(): void
    {
        $this->actingAs($this->cajero());

        $this->get('/facturas')->assertOk();
        $this->get('/clientes')->assertOk();
        $this->get('/')->assertOk();

        $this->get('/productos')->assertForbidden();
        $this->get('/usuarios')->assertForbidden();
        $this->get('/empresa')->assertForbidden();
        $this->get('/reportes/ventas')->assertForbidden();
    }

    public function test_el_menu_oculta_lo_que_no_puede_usar(): void
    {
        $this->actingAs($this->cajero())
            ->get('/')
            ->assertSee('Nueva factura')
            ->assertDontSee(route('usuarios.index'))
            ->assertDontSee(route('productos.index'));
    }

    public function test_el_cajero_no_puede_anular_sin_permiso(): void
    {
        $producto = Producto::where('codigo', 'BEB-001')->first();

        $this->actingAs($this->cajero())->post('/facturas', [
            'tipo_ncf' => 'B02',
            'fecha' => now()->toDateString(),
            'condicion' => 'CONTADO',
            'productos' => [['id' => $producto->id, 'cantidad' => 1]],
        ]);

        $factura = Factura::first();

        $this->get("/facturas/{$factura->id}")->assertOk()->assertDontSee('modalAnular');
        $this->patch("/facturas/{$factura->id}/anular", ['motivo' => 'prueba'])->assertForbidden();
        $this->assertEquals('EMITIDA', $factura->fresh()->estado);
    }

    public function test_el_admin_crea_un_usuario_con_permisos(): void
    {
        $this->actingAs($this->admin())->post('/usuarios', [
            'name' => 'Luis Vendedor',
            'email' => 'luis@factplus.do',
            'password' => 'clave12345',
            'password_confirmation' => 'clave12345',
            'activo' => '1',
            'permisos' => ['facturas', 'reportes'],
        ])->assertRedirect(route('usuarios.index'));

        $luis = User::where('email', 'luis@factplus.do')->first();
        $this->assertTrue($luis->puede('facturas'));
        $this->assertTrue($luis->puede('reportes'));
        $this->assertFalse($luis->puede('usuarios'));
    }

    public function test_usuario_desactivado_no_puede_entrar(): void
    {
        $this->cajero()->update(['activo' => false]);

        $this->post('/login', ['email' => 'cajero@factplus.do', 'password' => 'demo1234'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_activo_si_puede_entrar(): void
    {
        $this->post('/login', ['email' => 'cajero@factplus.do', 'password' => 'demo1234'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->cajero());
    }

    public function test_si_lo_desactivan_con_la_sesion_abierta_lo_saca(): void
    {
        $cajero = $this->cajero();
        $this->actingAs($cajero);
        $cajero->update(['activo' => false]);

        $this->get('/facturas')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_el_admin_no_puede_quitarse_su_propio_acceso(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put("/usuarios/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'activo' => '1',
            'permisos' => ['facturas'],
        ])->assertSessionHasErrors();

        $this->assertTrue($admin->fresh()->es_admin);
    }

    public function test_cambiar_mi_contrasena(): void
    {
        $cajero = $this->cajero();

        $this->actingAs($cajero)->put('/perfil/clave', [
            'clave_actual' => 'incorrecta',
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
        ])->assertSessionHasErrors('clave_actual');

        $this->actingAs($cajero)->put('/perfil/clave', [
            'clave_actual' => 'demo1234',
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(password_verify('nueva12345', $cajero->fresh()->password));
    }

    public function test_paginas_de_usuarios_cargan(): void
    {
        $this->actingAs($this->admin());

        $this->get('/usuarios')->assertOk()->assertSee('cajero@factplus.do');
        $this->get('/usuarios/create')->assertOk();
        $this->get('/usuarios/' . $this->cajero()->id . '/edit')->assertOk();
        $this->get('/perfil/clave')->assertOk();
    }
}

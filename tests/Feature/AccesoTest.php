<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccesoTest extends TestCase
{
    // Sin iniciar sesion debe mandar al login
    public function test_redirige_al_login_sin_sesion(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/facturas')->assertRedirect('/login');
    }

    public function test_pagina_de_login_carga(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión', false);
    }
}

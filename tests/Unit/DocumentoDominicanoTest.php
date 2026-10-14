<?php

namespace Tests\Unit;

use App\Rules\DocumentoDominicano;
use PHPUnit\Framework\TestCase;

class DocumentoDominicanoTest extends TestCase
{
    public function test_cedula_valida(): void
    {
        $this->assertTrue(DocumentoDominicano::cedulaValida('00112345673'));
        $this->assertTrue(DocumentoDominicano::cedulaValida('40223456787'));
    }

    public function test_cedula_con_digito_incorrecto(): void
    {
        $this->assertFalse(DocumentoDominicano::cedulaValida('00112345670'));
        $this->assertFalse(DocumentoDominicano::cedulaValida('abc12345673'));
    }

    public function test_rnc_valido(): void
    {
        $this->assertTrue(DocumentoDominicano::rncValido('130123454'));
        $this->assertTrue(DocumentoDominicano::rncValido('101456787'));
    }

    public function test_rnc_con_digito_incorrecto(): void
    {
        $this->assertFalse(DocumentoDominicano::rncValido('130123450'));
    }
}

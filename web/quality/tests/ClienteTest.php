<?php

namespace App\Tests;

use Cliente;
use DateTime;
use PHPUnit\Framework\TestCase;

require_once 'include_propel_tests.inc.php';

/**
 * Class ClienteTest
 *
 * @author Jesse Quinn
 * @package App\Tests
 */
class ClienteTest extends TestCase
{
    /**
     * Test Generation of sponsorship key.
     *
     * @throws \PropelException
     */
    public function testGeracaoChaveIndicacao()
    {
        $cliente1 = new Cliente();
        $this->assertNull($cliente1->getChaveIndicacao());
        $cliente1->gerarChaveIndicacao();
        $this->assertNotNull($cliente1->getChaveIndicacao());
        $cliente2 = new Cliente();
        $cliente2->gerarChaveIndicacao();
        $this->assertEquals(Cliente::TAMANHO_CHAVE_INDICACAO, strlen($cliente2->getChaveIndicacao()));
        $this->assertNotEquals($cliente1->getChaveIndicacao(), $cliente2->getChaveIndicacao());
        $str = preg_replace('/[^0-9]/', '', $cliente2->getChaveIndicacao());
        $this->assertEquals(Cliente::TAMANHO_CHAVE_INDICACAO, strlen($str));
        $cliente1->delete();
        $cliente2->delete();
    }

    /**
     * @throws \PropelException
     */
    public function testMensalidadeEmDia()
    {
        $now = new DateTime('now');
        $cliente1 = new Cliente();
        $this->assertFalse($cliente1->isMensalidadeEmDia($now));
        $cliente2 = new Cliente();
        $cliente2->setLivreMensalidade(true);
        $this->assertTrue($cliente2->isMensalidadeEmDia($now));
        $cliente3 = new Cliente();
        $cliente3->setLivreMensalidade(false);
        $vencimento = clone $now;
        $vencimento->modify('+1 day');
        $cliente3->setVencimentoMensalidade($vencimento);
        $this->assertTrue($cliente3->isMensalidadeEmDia($now));
        $cliente4 = new Cliente();
        $cliente4->setLivreMensalidade(false);
        $vencimento = clone $now;
        $vencimento->modify('-1 day');
        $cliente4->setVencimentoMensalidade($vencimento);
        $this->assertFalse($cliente4->isMensalidadeEmDia($now));
        $cliente4->setLivreMensalidade(true);
        $this->assertTrue($cliente4->isMensalidadeEmDia($now));
        $cliente1->delete();
        $cliente2->delete();
        $cliente3->delete();
        $cliente4->delete();
    }
}

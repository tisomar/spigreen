<?php

require_once './MyDatabaseTestCase.php';

/**
 * Description of GerenciadorParticipacaoResultadosTest
 *
 * @author André Garlini
 */
class GerenciadorParticipacaoResultadosTest extends MyDatabaseTestCase
{
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/myXmlFixture.xml');
    }
    
    /**
     * @group participacao_resultados
     */
    public function testClienteQualificado()
    {
        $con = Propel::getConnection();
        
        $plano = new Plano();
        $plano->setParticipacaoLucros(true);
        $plano->save($con);
        
        $this->criarRedeTeste($plano);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(100);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(120);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(150);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $produtoNormal1 = new Produto();
        $produtoNormal1->setNome('Produto 1');
        $produtoNormal1->setParticipacaoResultados(false);
        $produtoNormal1->save($con);
        $produtoNormal1->getProdutoVariacao()->setValorBase(20);
        $produtoNormal1->getProdutoVariacao()->save($con);
        
        $produtoNormal2 = new Produto();
        $produtoNormal2->setNome('Produto 2');
        $produtoNormal2->setParticipacaoResultados(false);
        $produtoNormal2->save($con);
        $produtoNormal2->getProdutoVariacao()->setValorBase(30);
        $produtoNormal2->getProdutoVariacao()->save($con);
                
        $c11 = ClienteQuery::create()->findOneByNome('1.1');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c11);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produtoNormal1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido1, true);
        
        $c21 = ClienteQuery::create()->findOneByNome('2.1');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c21);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produtoNormal2->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido2, true);
        
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c22);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido3, true);
        
        //deve qualificar (1 aparelho pedido1 + 2 aparelhos pedido2 + 2 apararelhos pedido3) >= 5
        $this->assertTrue($gerenciador->isClienteQualificadoParaParticipacaoResultados($c11));
        
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c21));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c22));
        
        $this->assertEquals(1, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        //Cancela o pedido1. agora não deve mais qualificar
        $pedido1->setStatus(PedidoPeer::STATUS_CANCELADO);
        $pedido1->save();
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c11));
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        
        //*********** Testa outro cliente
        $c14 = ClienteQuery::create()->findOneByNome('1.4');
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($c14);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido4, true);
        
        $c25 = ClienteQuery::create()->findOneByNome('2.5');
        
        $pedido5 = new Pedido();
        $pedido5->setCliente($c25);
        $pedido5->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido5);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido5, true);
        
        $c26 = ClienteQuery::create()->findOneByNome('2.6');
        
        $pedido6 = new Pedido();
        $pedido6->setCliente($c26);
        $pedido6->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido6);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido6, true);
        
        $c36 = ClienteQuery::create()->findOneByNome('3.6');
        
        $pedido7 = new Pedido();
        $pedido7->setCliente($c36);
        $pedido7->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido7);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido7, true);
        
        //existem 5 aparelhos comprados na arvore do cliente 1.4, mas o cliente 3.6 não é descendente direto, logo não deve contar o pedido7.
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c14));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c25));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c26));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c36));
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
    }
    
    /**
     * @group participacao_resultados
     */
    public function testClienteQualificado2()
    {
        $con = Propel::getConnection();
        
        $plano = new Plano();
        $plano->setParticipacaoLucros(true);
        $plano->save($con);
        
        $this->criarRedeTeste($plano);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(100);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(120);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(150);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $c21 = ClienteQuery::create()->findOneByNome('2.1');
        
        //testa sem pedidos
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c21));
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c21);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido1, true);
        
        $c31 = ClienteQuery::create()->findOneByNome('3.1');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c31);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido2, true);
        
        $c32 = ClienteQuery::create()->findOneByNome('3.2');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c32);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido3, true);
        
        // (2 + 2 + 3) > 5
        $this->assertTrue($gerenciador->isClienteQualificadoParaParticipacaoResultados($c21));
        
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c31));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c32));
        
        $this->assertEquals(1, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        //faz a data do pedido3 ser anterior a 3 meses. Não deve mais qualificar o cliente
        $pedido3->setCreatedAt(new DateTime('-100 days'));
        $pedido3->save($con);
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c21));
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
                
        //*********** Testa outro cliente
        
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($c22);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        //finaliza SEM confirmar pagamento
        $this->finalizaPedido($pedido4, false);
        
        $c33 = ClienteQuery::create()->findOneByNome('3.3');
        
        $pedido5 = new Pedido();
        $pedido5->setCliente($c33);
        $pedido5->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido5);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido5, true);
        
        $c34 = ClienteQuery::create()->findOneByNome('3.4');
        
        $pedido6 = new Pedido();
        $pedido6->setCliente($c34);
        $pedido6->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido6);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido6, true);
        
        //pagamento do pedido 4 ainda não foi confirmado. Cliente 2.2 não é qualificado ainda.
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c22));
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $pedido4->avancaStatus(false);
        //agora sim
        $this->assertTrue($gerenciador->isClienteQualificadoParaParticipacaoResultados($c22));
        
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c33));
        $this->assertFalse($gerenciador->isClienteQualificadoParaParticipacaoResultados($c34));
        
        $this->assertEquals(1, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
    }
    
    /**
     * @group participacao_resultados
     */
    public function testTotalVendasParticipacaoResultados()
    {
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorParticipacaoResultados($con = Propel::getConnection());
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(20.75);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(50.42);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(100);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $produto1 = new Produto();
        $produto1->setNome('Produto 1');
        $produto1->setParticipacaoResultados(false);
        $produto1->save($con);
        $produto1->getProdutoVariacao()->setValorBase(50);
        $produto1->getProdutoVariacao()->save($con);
        
        $produto2 = new Produto();
        $produto2->setNome('Produto 2');
        $produto2->setParticipacaoResultados(false);
        $produto2->save($con);
        $produto2->getProdutoVariacao()->setValorBase(100);
        $produto2->getProdutoVariacao()->save($con);
        
        $c11 = ClienteQuery::create()->findOneByNome('1.1');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c11);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produto1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produto2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido1, true);
        
        //pedido1 não possui nenhum aparelho. Total deve ser 0.
        $this->assertEquals(0.0, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(0.0, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
        
        $c12 = ClienteQuery::create()->findOneByNome('1.2');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c12);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produto1->getProdutoVariacao());
        $itemPedido->setQuantidade(5);
        $itemPedido->setValorUnitario($produto1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido2, false);
        
        //pedido2 possui aparelhos, mas o pagamento do pedido não foi confirmado.
        $this->assertEquals(0.0, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(0.0, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
        
        //aprova pedido2
        $pedido2->avancaStatus(false);
        
        //agora o total do pedido2 deve contar
        $this->assertEquals(192.76, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(17.34, $gerenciador->getTotalDistribuirProximaParticipacaoResultados(), '', 0.01);
        
        $c13 = ClienteQuery::create()->findOneByNome('1.3');
                
        $pedido3 = new Pedido();
        $pedido3->setCliente($c13);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido3, true);
        
        $this->assertEquals(392.76, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(35.35, $gerenciador->getTotalDistribuirProximaParticipacaoResultados(), '', 0.01);
        
        //cancela o pedido 2
        $pedido2->setStatus(PedidoPeer::STATUS_CANCELADO);
        $pedido2->save();
        
        $this->assertEquals(200.0, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(18.0, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
    }
    
    /**
     * @group participacao_resultados
     */
    public function testDistribuicao()
    {
        $con = Propel::getConnection();
        
        $plano = new Plano();
        $plano->setParticipacaoLucros(true);
        $plano->save($con);
        
        $this->criarRedeTeste($plano);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(25);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(50);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(100);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $c14 = ClienteQuery::create()->findOneByNome('1.4');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c14);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido1);
        
        $participacao = new ParticipacaoResultado();
        $participacao->setData(new DateTime());
        $participacao->save($con);
        
        $gerenciador->geraPreview($participacao);
        
        //cliente 1.4 fez um pedido, mas a quantidade não é suficiente.
        $this->assertNull($this->findParticipacaoCliente($participacao, $c14));
        
        $c25 = ClienteQuery::create()->findOneByNome('2.5');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c25);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido2, true);
        
        $c26 = ClienteQuery::create()->findOneByNome('2.6');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c26);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido3, true);
        
        
        $c42 = ClienteQuery::create()->findOneByNome('4.2');
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($c42);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(5);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido4, true);
        
        // 750 = (100 * 1) + (25 * 2) + (50 * 2) + (100 * 2)
        $this->assertEquals(750.00, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(67.50, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
        
        $participacao2 = new ParticipacaoResultado();
        $participacao2->setData(new DateTime());
        $participacao2->save($con);
        
        $gerenciador->geraPreview($participacao2);
                
        // 75    = 10% de 750
        // 67.50 = 90% de 75
        // 22.50 = 67.50 / 3 (cliente 1.4, 4.2 e 3.5 dividem a participação em 3 pois são os unicos qualificados - OBS: cliente 3.5 receberá a participação mesmo sem pedidos pois é o patrocinador de 4.2)
        //10% ficam para o proximo ciclo
        $this->assertEquals(22.50, $this->findParticipacaoCliente($participacao2, $c14)->getTotalPontos());
        $this->assertEquals(22.50, $this->findParticipacaoCliente($participacao2, $c42)->getTotalPontos());
        $this->assertEquals(22.50, $this->findParticipacaoCliente($participacao2, ($c35 = ClienteQuery::create()->findOneByNome('3.5')))->getTotalPontos());
        
        //tiveram pedidos, mas não são qualificados
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c25));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c26));
        
        $this->assertEquals(3, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $this->assertEquals(67.50, $participacao2->getTotalPontos());
        
        //confirma a distribuição
        $participacao2->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao2->save();
        $gerenciador->confirmaParticipacaoResultado($participacao2);
        
        //verifica se o cliente recebeu os pontos
        $this->assertEquals(22.50, $this->findUltimoExtratoCliente($participacao2, $c14)->getPontos());
        $this->assertEquals(22.50, $this->findUltimoExtratoCliente($participacao2, $c42)->getPontos());
        $this->assertEquals(22.50, $this->findUltimoExtratoCliente($participacao2, $c35)->getPontos());
        
        //tiveram pedidos, mas não são qualificados
        $this->assertNull($this->findUltimoExtratoCliente($participacao2, $c25));
        $this->assertNull($this->findUltimoExtratoCliente($participacao2, $c26));
    }
    
    
    /**
     * @group participacao_resultados
     */
    public function testDistribuicao2()
    {
        //Faz um testes com varios ciclos.
        
        $con = Propel::getConnection();
        
        $plano = new Plano();
        $plano->setParticipacaoLucros(true);
        $plano->save($con);
        
        $this->criarRedeTeste($plano);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(50);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(100);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(200);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $c21 = ClienteQuery::create()->findOneByNome('2.1');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c21);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido1, true);
        
        $c31 = ClienteQuery::create()->findOneByNome('3.1');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c31);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido2, true);
        
        $c32 = ClienteQuery::create()->findOneByNome('3.2');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c32);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido3);
        
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($c22);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido4, true);
        
        $c33 = ClienteQuery::create()->findOneByNome('3.3');
        
        $pedido5 = new Pedido();
        $pedido5->setCliente($c33);
        $pedido5->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido5);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido5, true);
        
        $c34 = ClienteQuery::create()->findOneByNome('3.4');
        
        $pedido6 = new Pedido();
        $pedido6->setCliente($c34);
        $pedido6->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido6);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido6, true);
        
        $c14 = ClienteQuery::create()->findOneByNome('1.4');
        
        $pedido7 = new Pedido();
        $pedido7->setCliente($c14);
        $pedido7->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido7);
        $itemPedido->save($con);
        
        //total: 50
        $this->finalizaPedido($pedido7, true);
        
        $c25 = ClienteQuery::create()->findOneByNome('2.5');
        
        $pedido8 = new Pedido();
        $pedido8->setCliente($c25);
        $pedido8->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido8);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido8, true);
        
        $c26 = ClienteQuery::create()->findOneByNome('2.6');
        
        $pedido9 = new Pedido();
        $pedido9->setCliente($c26);
        $pedido9->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido9);
        $itemPedido->save($con);
        
        $itemPedido2 = new PedidoItem();
        $itemPedido2->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido2->setQuantidade(1);
        $itemPedido2->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido2->setPedido($pedido9);
        $itemPedido2->save($con);
        
        //total: 250
        $this->finalizaPedido($pedido9, true);
        
        $c36 = ClienteQuery::create()->findOneByNome('3.6');
        
        $pedido10 = new Pedido();
        $pedido10->setCliente($c36);
        $pedido10->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido10);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido10, true);
        
        $this->assertEquals(1500, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(135.0, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
                
        $participacao = new ParticipacaoResultado();
        $participacao->setData(new DateTime());
        $participacao->save($con);
        
        $gerenciador->geraPreview($participacao);
        
        //1500 total
        //150 (10% 1500)
        //135 (90% 150)
        //45 (135 / 3)
        //clientes 2.1, 2.2 e 1.4 devem receber participacao nos resultados
        $this->assertEquals(45, $this->findParticipacaoCliente($participacao, $c21)->getTotalPontos());
        $this->assertEquals(45, $this->findParticipacaoCliente($participacao, $c22)->getTotalPontos());
        $this->assertEquals(45, $this->findParticipacaoCliente($participacao, $c14)->getTotalPontos());
        
        //esses tiveram pedidos, mas nao sao qualificados a receber a participacao nos resultados
        $this->assertNull($this->findParticipacaoCliente($participacao, $c31));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c32));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c33));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c34));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c25));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c26));
        $this->assertNull($this->findParticipacaoCliente($participacao, $c36));
        
        $this->assertEquals(3, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $participacao->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao->save($con);
        $gerenciador->confirmaParticipacaoResultado($participacao);
        
        $this->assertEquals(45, $this->findUltimoExtratoCliente($participacao, $c21)->getPontos());
        $this->assertEquals(45, $this->findUltimoExtratoCliente($participacao, $c22)->getPontos());
        $this->assertEquals(45, $this->findUltimoExtratoCliente($participacao, $c14)->getPontos());
        
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c31));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c32));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c33));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c34));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c25));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c26));
        $this->assertNull($this->findUltimoExtratoCliente($participacao, $c36));
        
        //totais
        $this->assertEquals(135, $participacao->getTotalPontos());
        $this->assertEquals(1500, $participacao->getTotalPontosProcessados());
        $this->assertEquals(15, $participacao->getTotalPontosRestantes());
        
        
        /****** inicia um novo ciclo *******/
        //vamos simular que passaram 4 meses
        $data = new DateTime('+4 months');
                
        $pedido11 = new Pedido();
        $pedido11->setCliente($c21);
        $pedido11->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido11);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido11, true, $data);
        
        $pedido12 = new Pedido();
        $pedido12->setCliente($c31);
        $pedido12->save();
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido12);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido12, true, $data);
        
        $pedido13 = new Pedido();
        $pedido13->setCliente($c32);
        $pedido13->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido13);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido13, true, $data);
        
        $pedido14 = new Pedido();
        $pedido14->setCliente($c22);
        $pedido14->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido14);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido14, true, $data);
        
        $pedido15 = new Pedido();
        $pedido15->setCliente($c33);
        $pedido15->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido15);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido15, true, $data);
        
        $pedido16 = new Pedido();
        $pedido16->setCliente($c34);
        $pedido16->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido16);
        $itemPedido->save($con);
        
        //total: 400
        $this->finalizaPedido($pedido16, true, $data);
        
        $pedido17 = new Pedido();
        $pedido17->setCliente($c14);
        $pedido17->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido17);
        $itemPedido->save($con);
        
        //total: 150
        $this->finalizaPedido($pedido17, true, $data);
        
        $pedido18 = new Pedido();
        $pedido18->setCliente($c25);
        $pedido18->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido18);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido18, true, $data);
        
        $pedido19 = new Pedido();
        $pedido19->setCliente($c26);
        $pedido19->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido19);
        $itemPedido->save($con);
        
        //total: 400
        $this->finalizaPedido($pedido19, true, $data);
        
        $pedido20 = new Pedido();
        $pedido20->setCliente($c36);
        $pedido20->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(4);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido20);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido20, true, $data);
        
        $this->assertEquals(3450, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(189, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
        
        $participacao2 = new ParticipacaoResultado();
        $participacao2->setData($data);
        $participacao->save($con);
        
        $gerenciador->geraPreview($participacao2);
        
        //total distribuir neste ciclo: 1950 (3450 - 1500) = (total geral - processado ciclo anterior)
        //210 = 195 + 15 (10% 1950 + 15 restantes ciclo anterior)
        //189 = (90% de 210)
        //47.25 = (189 / 4)
        //clientes 2.1, 2.2, 1.4 e 2.6 devem receber a participacao nos resultados
        
        $this->assertEquals(47.25, $this->findParticipacaoCliente($participacao2, $c21)->getTotalPontos());
        $this->assertEquals(47.25, $this->findParticipacaoCliente($participacao2, $c22)->getTotalPontos());
        $this->assertEquals(47.25, $this->findParticipacaoCliente($participacao2, $c14)->getTotalPontos());
        $this->assertEquals(47.25, $this->findParticipacaoCliente($participacao2, $c26)->getTotalPontos());
        
        //tiveram pedidos, mas nao devem participar da distribuicao
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c31));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c32));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c33));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c34));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c25));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c36));
        
        $this->assertEquals(4, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao2->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao2->save($con);
        $gerenciador->confirmaParticipacaoResultado($participacao2);
        
        $this->assertEquals(47.25, $this->findUltimoExtratoCliente($participacao2, $c21)->getPontos());
        $this->assertEquals(47.25, $this->findUltimoExtratoCliente($participacao2, $c22)->getPontos());
        $this->assertEquals(47.25, $this->findUltimoExtratoCliente($participacao2, $c14)->getPontos());
        $this->assertEquals(47.25, $this->findUltimoExtratoCliente($participacao2, $c26)->getPontos());
        
        //totais
        $this->assertEquals(189, $participacao2->getTotalPontos());
        $this->assertEquals(1950, $participacao2->getTotalPontosProcessados());
        $this->assertEquals(21, $participacao2->getTotalPontosRestantes());
        
        /****** inicia outro ciclo *******/
        //vamos simular que passaram outros 4 meses
        $data = $data->modify('+4 months');
        
        $pedido21 = new Pedido();
        $pedido21->setCliente($c21);
        $pedido21->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido21);
        $itemPedido->save($con);
        
        //total: 150
        $this->finalizaPedido($pedido21, true, $data);
                      
        $pedido22 = new Pedido();
        $pedido22->setCliente($c31);
        $pedido22->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido22);
        $itemPedido->save($con);
        
        $itemPedido2 = new PedidoItem();
        $itemPedido2->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido2->setQuantidade(1);
        $itemPedido2->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido2->setPedido($pedido22);
        $itemPedido2->save($con);
        
        //total: 300
        $this->finalizaPedido($pedido22, true, $data);
        
        $pedido23 = new Pedido();
        $pedido23->setCliente($c32);
        $pedido23->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido23);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido23, true, $data);
        
        $pedido24 = new Pedido();
        $pedido24->setCliente($c14);
        $pedido24->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(4);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido24);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido24, true, $data);
        
        $pedido25 = new Pedido();
        $pedido25->setCliente($c25);
        $pedido25->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(4);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido25);
        $itemPedido->save($con);
        
        //total: 400
        $this->finalizaPedido($pedido25, true, $data);
        
        $pedido26 = new Pedido();
        $pedido26->setCliente($c26);
        $pedido26->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido26);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido26, true, $data);
        
        $participacao3 = new ParticipacaoResultado();
        $participacao3->setData($data);
        $participacao3->save($con);
        
        $gerenciador->geraPreview($participacao3);
        
        //total distribuir neste ciclo: 1450 (4900 - 1500 - 1950) = (total geral - 1º ciclo - 2º ciclo)
        //166 = 145 + 21 (10% de 1450 + restante ciclo anterior)
        //149,40 = (90% de 166)
        //74,70 = (149,40 / 2)
        //apenas clientes 2.1 e 1.4 devem receber participacao neste ciclo
        
        $this->assertEquals(74.70, $this->findParticipacaoCliente($participacao3, $c21)->getTotalPontos());
        $this->assertEquals(74.70, $this->findParticipacaoCliente($participacao3, $c14)->getTotalPontos());
        
        //fizeram pedidos, mas nao participam dessa distribuicao
        $this->assertNull($this->findParticipacaoCliente($participacao3, $c31));
        $this->assertNull($this->findParticipacaoCliente($participacao3, $c32));
        $this->assertNull($this->findParticipacaoCliente($participacao3, $c25));
        $this->assertNull($this->findParticipacaoCliente($participacao3, $c26));
        
        $this->assertEquals(2, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao3->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao3->save($con);
        $gerenciador->confirmaParticipacaoResultado($participacao3);
        
        $this->assertEquals(74.70, $this->findUltimoExtratoCliente($participacao3, $c21)->getPontos());
        $this->assertEquals(74.70, $this->findUltimoExtratoCliente($participacao3, $c14)->getPontos());
        
        //totais
        $this->assertEquals(149.40, $participacao3->getTotalPontos());
        $this->assertEquals(1450, $participacao3->getTotalPontosProcessados());
        $this->assertEquals(16.6, $participacao3->getTotalPontosRestantes());
        
        /***** outro ciclo ******/
        //mais 4 meses
        $data = $data->modify('+4 months');
        
        $pedido27 = new Pedido();
        $pedido27->setCliente($c22);
        $pedido27->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido27);
        $itemPedido->save($con);
        
        //total: 50
        $this->finalizaPedido($pedido27, true, $data);
        
        $pedido28 = new Pedido();
        $pedido28->setCliente($c33);
        $pedido28->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido28);
        $itemPedido->save($con);
        
        //total: 300
        $this->finalizaPedido($pedido28, true, $data);
        
        $pedido29 = new Pedido();
        $pedido29->setCliente($c34);
        $pedido29->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido29);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido29, true, $data);
        
        $participacao4 = new ParticipacaoResultado();
        $participacao4->setData($data);
        $participacao4->save($con);
        
        $gerenciador->geraPreview($participacao4);
        
        //Total distribuir neste ciclo: 550 (5450 - 1500 - 1950 - 1450) = (total geral - 1º ciclo - 2º ciclo - 3° ciclo)
        // 71,60 = 55 +  16,6 (10% de 550 + restante ciclo anterior)
        // 64,44 = (90% de 71,60)
        // 64,44 = 64,44 / 1
        //cliente 2.2 é o unico que recebera a participacao neste ciclo
        
        $this->assertEquals(64.44, $this->findParticipacaoCliente($participacao4, $c22)->getTotalPontos());
        
        //nenhum outro deve receber participacao
        $colClientes = ClienteQuery::create()->find($con);
        foreach ($colClientes as $cliente) {
            if ($cliente->getId() != $c22->getId()) {
                $this->assertNull($this->findUltimoExtratoCliente($participacao4, $cliente));
            }
        }
        
        $this->assertEquals(1, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao4->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao4->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao4);
        
        $this->assertEquals(64.44, $this->findUltimoExtratoCliente($participacao4, $c22)->getPontos());
        
        //totais
        $this->assertEquals(64.44, $participacao4->getTotalPontos());
        $this->assertEquals(550, $participacao4->getTotalPontosProcessados());
        $this->assertEquals(7.16, $participacao4->getTotalPontosRestantes());
    }
    
    
    /**
     * @group participacao_resultados
     */
    public function testDistribuicao3()
    {
        //Faz um teste que possui um ciclo vazio (sem distribuições).
        
        $con = Propel::getConnection();
        
        $plano = new Plano();
        $plano->setParticipacaoLucros(true);
        $plano->save($con);
        
        $this->criarRedeTeste($plano);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(50);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(100);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(200);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $data = new DateTime();
        
        $c21 = ClienteQuery::create()->findOneByNome('2.1');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c21);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido1, true, $data);
        
        $c31 = ClienteQuery::create()->findOneByNome('3.1');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c31);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido2, true, $data);
        
        $c32 = ClienteQuery::create()->findOneByNome('3.2');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c32);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido3, true, $data);
        
        $root = ClienteQuery::create()->findOneByNome('root');
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($root);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(5);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        //total: 250
        $this->finalizaPedido($pedido4, true, $data);
        
        $participacao1 = new ParticipacaoResultado();
        $participacao1->setData($data);
        $participacao1->save($con);
        
        $gerenciador->geraPreview($participacao1);
        
        //total pedidos 750
        //75 = (10% de 750)
        //67,5 = (90% de 75)
        //33,75 = (67,5 / 2)
        //clientes 2.1 e root devem receber participacao
        
        $this->assertEquals(33.75, $this->findParticipacaoCliente($participacao1, $c21)->getTotalPontos());
        $this->assertEquals(33.75, $this->findParticipacaoCliente($participacao1, $root)->getTotalPontos());
        
        $this->assertEquals(2, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $participacao1->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao1->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao1);
        
        $this->assertEquals(33.75, $this->findUltimoExtratoCliente($participacao1, $c21)->getPontos());
        $this->assertEquals(33.75, $this->findUltimoExtratoCliente($participacao1, $root)->getPontos());
        
        //totais
        $this->assertEquals(67.50, $participacao1->getTotalPontos());
        $this->assertEquals(750, $participacao1->getTotalPontosProcessados());
        $this->assertEquals(7.50, $participacao1->getTotalPontosRestantes());
        
        
        /**** testa um ciclo que tenha pedidos, mas que nenhum cliente receba participacao *****/
        $data = $data->modify('+4 months');
        
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        
        $pedido5 = new Pedido();
        $pedido5->setCliente($c22);
        $pedido5->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido5);
        $itemPedido->save($con);
        
        //total: 150
        $this->finalizaPedido($pedido5, true, $data);
        
        $c25 = ClienteQuery::create()->findOneByNome('2.5');
        
        $pedido6 = new Pedido();
        $pedido6->setCliente($c25);
        $pedido6->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido6);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido6, true, $data);
        
        $participacao2 = new ParticipacaoResultado();
        $participacao2->setData($data);
        $participacao2->save($con);
        
        $gerenciador->geraPreview($participacao2);
        
        $clientes = ClienteQuery::create()->find($con);
        
        //nenhum cliente deve receber participacao neste ciclo
        foreach ($clientes as $cliente) {
            $this->assertNull($this->findParticipacaoCliente($participacao2, $cliente));
        }
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao2->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao2->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao2);
        
        foreach ($clientes as $cliente) {
            $this->assertNull($this->findUltimoExtratoCliente($participacao2, $cliente));
        }
        
        //totais
        //350 = (1100 - 750) (total geral - 1º ciclo)
        //42,50 = 35 + 7,50  (10% de 350 + restante ciclo anterior)
        //38,25 = 90% de 42,50
        //4,25 = 10% de 42,50 (restante)
        
        $this->assertEquals(38.25, $participacao2->getTotalPontos());
        $this->assertEquals(350, $participacao2->getTotalPontosProcessados());
        $this->assertEquals(4.25, $participacao2->getTotalPontosRestantes());
        
        /****** terceiro ciclo ********/
        $data = $data->modify('+4 months');
        
        $c35 = ClienteQuery::create()->findOneByNome('3.5');
        
        $pedido7 = new Pedido();
        $pedido7->setCliente($c35);
        $pedido7->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(4);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido7);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido7, true, $data);
        
        $c42 = ClienteQuery::create()->findOneByNome('4.2');
        
        $pedido8 = new Pedido();
        $pedido8->setCliente($c42);
        $pedido8->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido8);
        $itemPedido->save($con);
        
        //total: 100
        $this->finalizaPedido($pedido8, true, $data);
        
        $c26 = ClienteQuery::create()->findOneByNome('2.6');
        
        $pedido9 = new Pedido();
        $pedido9->setCliente($c26);
        $pedido9->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido9);
        $itemPedido->save($con);
        
        //total: 150
        $this->finalizaPedido($pedido9, true, $data);
        
        $c36 = ClienteQuery::create()->findOneByNome('3.6');
        
        $pedido10 = new Pedido();
        $pedido10->setCliente($c36);
        $pedido10->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido10);
        $itemPedido->save($con);
        
        //total: 400
        $this->finalizaPedido($pedido10, true, $data);
        
        $participacao3 = new ParticipacaoResultado();
        $participacao3->setData($data);
        $participacao3->save($con);
        
        $gerenciador->geraPreview($participacao3);
        
        //total 850 (total geral - ciclos anteriores
        //89,25 = 85 + 4,25 (10% de 850 + restante ciclo anterior
        //80,325 = 90% de 89,25
        //40,1625 = 80,325 / 2 (clientes 3.5 e 2.6 receberao participacao)
        
        $this->assertEquals(40.1625, $this->findParticipacaoCliente($participacao3, $c35)->getTotalPontos());
        $this->assertEquals(40.1625, $this->findParticipacaoCliente($participacao3, $c26)->getTotalPontos());
        
        $this->assertEquals(2, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao3->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao3->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao3);
        
        $this->assertEquals(40.1625, $this->findUltimoExtratoCliente($participacao3, $c35)->getPontos());
        $this->assertEquals(40.1625, $this->findUltimoExtratoCliente($participacao3, $c26)->getPontos());
        
        //totais
        $this->assertEquals(80.325, $participacao3->getTotalPontos());
        $this->assertEquals(850, $participacao3->getTotalPontosProcessados());
        $this->assertEquals(8.925, $participacao3->getTotalPontosRestantes());
        
        /********  testa um ciclo sem pedidos  *********/
        $data = $data->modify('+4 months');
        
        $participacao4 = new ParticipacaoResultado();
        $participacao4->setData($data);
        $participacao4->save($con);
        
        $gerenciador->geraPreview($participacao4);
        
        //nenhum cliente deve participar
        foreach ($clientes as $cliente) {
            $this->assertNull($this->findParticipacaoCliente($participacao4, $cliente));
        }
        
        $this->assertEquals(0, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao4->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao4->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao4);
        
        //nenhum cliente deve ter extratos associados a participacao
        foreach ($clientes as $cliente) {
            $this->assertNull($this->findUltimoExtratoCliente($participacao4, $cliente));
        }
        
        //totais
        $this->assertEquals(0.0, $participacao4->getTotalPontos()); //nenhum ponto foi distribuido
        $this->assertEquals(0.0, $participacao4->getTotalPontosProcessados()); //nenhum ponto foi processado
        $this->assertEquals($participacao3->getTotalPontosRestantes(), $participacao4->getTotalPontosRestantes()); //o restante deve ficar o mesmo da distribuicao anterior
        
        /**** agora faz outro ciclo com pedidos *****/
        $data = $data->modify('+4 months');
        
        $c33 = ClienteQuery::create()->findOneByNome('3.3');
        
        $pedido11 = new Pedido();
        $pedido11->setCliente($c33);
        $pedido11->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido11);
        $itemPedido->save($con);
        
        //total: 150
        $this->finalizaPedido($pedido11, true, $data);
        
        $c41 = ClienteQuery::create()->findOneByNome('4.1');
        
        $pedido12 = new Pedido();
        $pedido12->setCliente($c41);
        $pedido12->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido12);
        $itemPedido->save($con);
        
        //total: 400
        $this->finalizaPedido($pedido12, true, $data);
        
        $c13 = ClienteQuery::create()->findOneByNome('1.3');
        
        $pedido13 = new Pedido();
        $pedido13->setCliente($c13);
        $pedido13->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(4);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido13);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido13, true, $data);
        
        $c24 = ClienteQuery::create()->findOneByNome('2.4');
        
        $pedido14 = new Pedido();
        $pedido14->setCliente($c24);
        $pedido14->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido14);
        $itemPedido->save($con);
        
        //total: 200
        $this->finalizaPedido($pedido14, true, $data);
        
        $participacao5 = new ParticipacaoResultado();
        $participacao5->setData($data);
        $participacao5->save($con);
        
        $gerenciador->geraPreview($participacao5);
        
        //total: 950 (total geral - ciclos anteriores
        //103,925 = 95 + 8,925 (10% de 950 + restante ciclo anterior)
        //93,5325 = 90% de 103,925
        //46,76625 = 93,5325 / 2
        //clientes 3.3 e 1.3 participarao da distribuicao
        
        $this->assertEquals(46.76625, $this->findParticipacaoCliente($participacao5, $c33)->getTotalPontos());
        $this->assertEquals(46.76625, $this->findParticipacaoCliente($participacao5, $c13)->getTotalPontos());
        
        $this->assertEquals(2, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados($data));
        
        $participacao5->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao5->save($con);
        
        $gerenciador->confirmaParticipacaoResultado($participacao5);
        
        $this->assertEquals(46.76625, $this->findUltimoExtratoCliente($participacao5, $c33)->getPontos());
        $this->assertEquals(46.76625, $this->findUltimoExtratoCliente($participacao5, $c13)->getPontos());
        
        //totais
        $this->assertEquals(93.5325, $participacao5->getTotalPontos());
        $this->assertEquals(950, $participacao5->getTotalPontosProcessados());
        $this->assertEquals(10.3925, $participacao5->getTotalPontosRestantes());
        
//        $this->assertEquals(750 + 350 + 850 + 950, $gerenciador->getTotalVendasParticipacaoResultados());
//
//        foreach (ClienteQuery::create()->find() as $cliente) {
//            if ($gerenciador->isClienteQualificadoParaParticipacaoResultados($cliente, $data)) {
//                echo "\n cliente {$cliente->getNome()} é qualificado.";
//            }
//        }
    }
    
    
    /**
     * @group participacao_resultados
     */
    public function testClienteForaRede()
    {
        //Estava ocorrendo um erro quando existia clientes ainda não inseridos na rede.
        //Este teste verifica se a situação está sendo tratada.
        
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorParticipacaoResultados($con = Propel::getConnection());
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(50);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $novo1 = new Cliente();
        $novo1->setNome('Novo 1');
        $novo1->save($con);
        
        $novo2 = new Cliente();
        $novo2->setNome('Novo 2');
        $novo2->save($con);
        
        $pedido = new Pedido();
        $pedido->setCliente($c11 = ClienteQuery::create()->findOneByNome('1.1'));
        $pedido->save($con);
        
        $participacao1 = new ParticipacaoResultado();
        $participacao1->setData(new DateTime());
        $participacao1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido, true);
        
        $gerenciador->geraPreview($participacao1);
        
        $participacao1->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        
        $gerenciador->confirmaParticipacaoResultado($participacao1);
    }
    
    /**
     * @group participacao_resultados
     */
    public function testDistribuicaoSemBonusNoPlano()
    {
        /*
         * Certifica que o patrocinador não recebe bonus se o plano dele não possuir este bonus
         */
        
        $con = Propel::getConnection();
        
        $planoSemBonus = new Plano();
        $planoSemBonus->setParticipacaoLucros(false);
        $planoSemBonus->save($con);
        
        $planoComBonus = new Plano();
        $planoComBonus->setParticipacaoLucros(true);
        $planoComBonus->save($con);
        
        $this->criarRedeTeste($planoComBonus);
        
        $gerenciador = new GerenciadorParticipacaoResultados($con);
        
        $aparelho1 = new Produto();
        $aparelho1->setNome('Aparelho 1');
        $aparelho1->setParticipacaoResultados(true);
        $aparelho1->save($con);
        $aparelho1->getProdutoVariacao()->setValorBase(25);
        $aparelho1->getProdutoVariacao()->save($con);
        
        $aparelho2 = new Produto();
        $aparelho2->setNome('Aparelho 2');
        $aparelho2->setParticipacaoResultados(true);
        $aparelho2->save($con);
        $aparelho2->getProdutoVariacao()->setValorBase(50);
        $aparelho2->getProdutoVariacao()->save($con);
        
        $aparelho3 = new Produto();
        $aparelho3->setNome('Aparelho 3');
        $aparelho3->setParticipacaoResultados(true);
        $aparelho3->save($con);
        $aparelho3->getProdutoVariacao()->setValorBase(100);
        $aparelho3->getProdutoVariacao()->save($con);
        
        $c14 = ClienteQuery::create()->findOneByNome('1.4');
        
        $pedido1 = new Pedido();
        $pedido1->setCliente($c14);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido1);
        
        $participacao = new ParticipacaoResultado();
        $participacao->setData(new DateTime());
        $participacao->save($con);
        
        $gerenciador->geraPreview($participacao);
        
        //cliente 1.4 fez um pedido, mas a quantidade não é suficiente.
        $this->assertNull($this->findParticipacaoCliente($participacao, $c14));
        
        $c25 = ClienteQuery::create()->findOneByNome('2.5');
        
        $pedido2 = new Pedido();
        $pedido2->setCliente($c25);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho1->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho1->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido2, true);
        
        $c26 = ClienteQuery::create()->findOneByNome('2.6');
        
        $pedido3 = new Pedido();
        $pedido3->setCliente($c26);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho2->getProdutoVariacao());
        $itemPedido->setQuantidade(2);
        $itemPedido->setValorUnitario($aparelho2->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido3, true);
        
        
        $c42 = ClienteQuery::create()->findOneByNome('4.2');
        $c42->setPlano($planoSemBonus); /* deixa o cliente 4.2 sem bonus */
        $c42->save($con);
        
        $pedido4 = new Pedido();
        $pedido4->setCliente($c42);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($aparelho3->getProdutoVariacao());
        $itemPedido->setQuantidade(5);
        $itemPedido->setValorUnitario($aparelho3->getProdutoVariacao()->getValor());
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        $this->finalizaPedido($pedido4, true);
        
        // 750 = (100 * 1) + (25 * 2) + (50 * 2) + (100 * 2)
        $this->assertEquals(750.00, $gerenciador->getTotalVendasParticipacaoResultados());
        $this->assertEquals(67.50, $gerenciador->getTotalDistribuirProximaParticipacaoResultados());
        
        $participacao2 = new ParticipacaoResultado();
        $participacao2->setData(new DateTime());
        $participacao2->save($con);
        
        $gerenciador->geraPreview($participacao2);
                
        // 75    = 10% de 750
        // 67.50 = 90% de 75
        // 33.75 = 67.50 / 2 (cliente 1.4 e 3.5 dividem a participação em 2 pois são os unicos qualificados
        // - OBS: cliente 3.5 receberá a participação mesmo sem pedidos pois é o patrocinador de 4.2 - OBS2: 4.2 não recebe por causa do plano)
        //10% ficam para o proximo ciclo
        $this->assertEquals(33.75, $this->findParticipacaoCliente($participacao2, $c14)->getTotalPontos());
        $this->assertEquals(33.75, $this->findParticipacaoCliente($participacao2, ($c35 = ClienteQuery::create()->findOneByNome('3.5')))->getTotalPontos());
        
        //nao deve receber, pois seu plano nao permite participacao nos lucros
        $this->assertEquals(null, $this->findParticipacaoCliente($participacao2, $c42));
        
        //tiveram pedidos, mas não são qualificados
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c25));
        $this->assertNull($this->findParticipacaoCliente($participacao2, $c26));
        
        $this->assertEquals(3, $gerenciador->getTotalClientesQualificadosParaParticipacaoResultados());
        
        $this->assertEquals(67.50, $participacao2->getTotalPontos());
        
        //confirma a distribuição
        $participacao2->setStatus(ParticipacaoResultado::STATUS_AGUARDANDO);
        $participacao2->save();
        $gerenciador->confirmaParticipacaoResultado($participacao2);
        
        //verifica se o cliente recebeu os pontos
        $this->assertEquals(33.75, $this->findUltimoExtratoCliente($participacao2, $c14)->getPontos());
        $this->assertEquals(33.75, $this->findUltimoExtratoCliente($participacao2, $c35)->getPontos());
        
        //nao recebeu por conta do plano
        $this->assertEquals(null, $this->findUltimoExtratoCliente($participacao2, $c42));
        
        //tiveram pedidos, mas não são qualificados
        $this->assertNull($this->findUltimoExtratoCliente($participacao2, $c25));
        $this->assertNull($this->findUltimoExtratoCliente($participacao2, $c26));
    }
        
        
    /**
     *
     * @param ParticipacaoResultado $participacao
     * @param Cliente $cliente
     * @return ParticipacaoResultadoCliente|null
     */
    protected function findParticipacaoCliente(ParticipacaoResultado $participacao, Cliente $cliente)
    {
        return ParticipacaoResultadoClienteQuery::create()
                                        ->filterByParticipacaoResultado($participacao)
                                        ->filterByCliente($cliente)
                                        ->findOne();
    }
    
    /**
     *
     * @param ParticipacaoResultado $participacao
     * @param Cliente $cliente
     * @return Extrato|null
     */
    protected function findUltimoExtratoCliente(ParticipacaoResultado $participacao, Cliente $cliente)
    {
        return ExtratoQuery::create()
                        ->filterByParticipacaoResultado($participacao)
                        ->filterByCliente($cliente)
                        ->filterByTipo(Extrato::TIPO_PARTICIPACAO_RESULTADOS)
                        ->orderById(Criteria::DESC)
                        ->findOne();
    }


    protected function finalizaPedido(Pedido $pedido, $confirmaPagamento = true, $data = null)
    {
        if (null === $data) {
            $data = new DateTime();
        }
        
        $pedido->setClassKey(1);
        
        $pedido->setCreatedAt($data);
        $pedido->setUpdatedAt($data);
        
        $pedido->save();
        
        $pedidoFormaPagamento = new PedidoFormaPagamento();
        $pedidoFormaPagamento->setPedido($pedido);
        $pedidoFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
        $pedidoFormaPagamento->save();
                
        $pedido->avancaStatus(false);
        
        if ($confirmaPagamento) {
            $pedido->avancaStatus(false);
        }
        
        $pedido->save();
    }
    
    /**
     * Cria uma arvore de teste com a seguinte estrutura:
     *
     *                                      root
     *             1.1                1.2                    1.3         1.4
     *      2.1         2.2        2.3                   2.4          2.5     2.6
     *  3.1   3.2     3.3   3.4                      3.5                    3.6
     *             4.1                            4.2
     *
     */
    protected function criarRedeTeste(Plano $plano = null)
    {
        $con = Propel::getConnection();
                
        //root
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        if ($plano) {
            $root->setPlano($plano);
        }
        $root->save($con);
        
        //1.1
        $c11 = new Cliente();
        $c11->setNome('1.1');
        $c11->insertAsFirstChildOf($root);
        if ($plano) {
            $c11->setPlano($plano);
        }
        $c11->save($con);
        
        //1.2
        $c12 = new Cliente();
        $c12->setNome('1.2');
        $c12->insertAsLastChildOf($root);
        if ($plano) {
            $c12->setPlano($plano);
        }
        $c12->save($con);
        
        //1.3
        $c13 = new Cliente();
        $c13->setNome('1.3');
        $c13->insertAsLastChildOf($root);
        if ($plano) {
            $c13->setPlano($plano);
        }
        $c13->save($con);
        
        //1.4
        $c14 = new Cliente();
        $c14->setNome('1.4');
        $c14->insertAsLastChildOf($root);
        if ($plano) {
            $c14->setPlano($plano);
        }
        $c14->save($con);
        
        //2.1
        $c21 = new Cliente();
        $c21->setNome('2.1');
        $c21->insertAsFirstChildOf($c11);
        if ($plano) {
            $c21->setPlano($plano);
        }
        $c21->save($con);
        
        //2.2
        $c22 = new Cliente();
        $c22->setNome('2.2');
        $c22->insertAsLastChildOf($c11);
        if ($plano) {
            $c22->setPlano($plano);
        }
        $c22->save($con);
        
        //2.3
        $c23 = new Cliente();
        $c23->setNome('2.3');
        $c23->insertAsFirstChildOf($c12);
        if ($plano) {
            $c23->setPlano($plano);
        }
        $c23->save($con);
        
        //2.4
        $c24 = new Cliente();
        $c24->setNome('2.4');
        $c24->insertAsFirstChildOf($c13);
        if ($plano) {
            $c24->setPlano($plano);
        }
        $c24->save($con);
        
        //2.5
        $c25 = new Cliente();
        $c25->setNome('2.5');
        $c25->insertAsFirstChildOf($c14);
        if ($plano) {
            $c25->setPlano($plano);
        }
        $c25->save($con);
        
        //2.6
        $c26 = new Cliente();
        $c26->setNome('2.6');
        $c26->insertAsLastChildOf($c14);
        if ($plano) {
            $c26->setPlano($plano);
        }
        $c26->save($con);
        
        //3.1
        $c31 = new Cliente();
        $c31->setNome('3.1');
        $c31->insertAsFirstChildOf($c21);
        if ($plano) {
            $c31->setPlano($plano);
        }
        $c31->save($con);
        
        //3.2
        $c32 = new Cliente();
        $c32->setNome('3.2');
        $c32->insertAsLastChildOf($c21);
        if ($plano) {
            $c32->setPlano($plano);
        }
        $c32->save($con);
        
        //3.3
        $c33 = new Cliente();
        $c33->setNome('3.3');
        $c33->insertAsFirstChildOf($c22);
        if ($plano) {
            $c33->setPlano($plano);
        }
        $c33->save($con);
        
        //3.4
        $c34 = new Cliente();
        $c34->setNome('3.4');
        $c34->insertAsLastChildOf($c22);
        if ($plano) {
            $c34->setPlano($plano);
        }
        $c34->save($con);
        
        //3.5
        $c35 = new Cliente();
        $c35->setNome('3.5');
        $c35->insertAsFirstChildOf($c24);
        if ($plano) {
            $c35->setPlano($plano);
        }
        $c35->save($con);
        
        //3.6
        $c36 = new Cliente();
        $c36->setNome('3.6');
        $c36->insertAsFirstChildOf($c26);
        if ($plano) {
            $c36->setPlano($plano);
        }
        $c36->save($con);
        
        //4.1
        $c41 = new Cliente();
        $c41->setNome('4.1');
        $c41->insertAsFirstChildOf($c33);
        if ($plano) {
            $c41->setPlano($plano);
        }
        $c41->save($con);
        
        //4.2
        $c42 = new Cliente();
        $c42->setNome('4.2');
        $c42->insertAsFirstChildOf($c35);
        if ($plano) {
            $c42->setPlano($plano);
        }
        $c42->save($con);
    }
}

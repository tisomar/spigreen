<?php

require_once './MyDatabaseTestCase.php';

/**
 * Description of GerenciadorBonusRedeBinariaTest
 *
 * @author André Garlini
 */
class GerenciadorBonusRedeBinariaTest extends MyDatabaseTestCase
{
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/myXmlFixture.xml');
    }
    
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoHotfix()
    {
        //Faz um teste como o exemplo que o cliente enviou como ajuste
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano2 = new Plano();
        $plano2->setNome('Plano 2');
        $plano2->setGeracaoPontos(30);
        $plano2->setRedeBinaria(20);
        $plano2->save($con);
        
        $kitPlano2 = new Produto();
        $kitPlano2->setPlanoRelatedByPlanoId($plano2);
        $kitPlano2->setNome('Kit adesão plano 2');
        $kitPlano2->setValorPontos(90);
        $kitPlano2->getProdutoVariacao()->setValorBase(98.00);
        $kitPlano2->save($con);
        
        $this->assertEquals(120, $kitPlano2->getTotalPontosKitAdesao());
        
        $plano3 = new Plano();
        $plano3->setNome('Plano 3');
        $plano3->setGeracaoPontos(60);
        $plano3->setRedeBinaria(35);
        $plano3->save($con);
        
        $kitPlano3 = new Produto();
        $kitPlano3->setPlanoRelatedByPlanoId($plano3);
        $kitPlano3->setNome('Kit adesão plano 3');
        $kitPlano3->setValorPontos(180);
        $kitPlano3->getProdutoVariacao()->setValorBase(196.00);
        
        $this->assertEquals(240, $kitPlano3->getTotalPontosKitAdesao());
        
        //um produto normal
        $purificador = new Produto();
        $purificador->setNome('ÁGUA AR 450');
        $purificador->getProdutoVariacao()->setValorBase(980);
        $purificador->setValorPontos(180);
        $purificador->save($con);
        
        $plano4 = new Plano();
        $plano4->setNome('Plano 4');
        $plano4->setGeracaoPontos(90);
        $plano4->setRedeBinaria(50);
        $plano4->save($con);
        
        $kitPlano4 = new Produto();
        $kitPlano4->setPlanoRelatedByPlanoId($plano4);
        $kitPlano4->setNome('Kit adesão plano 4');
        $kitPlano4->setValorPontos(270);
        $kitPlano4->getProdutoVariacao()->setValorBase(294);
        
        $this->assertEquals(360, $kitPlano4->getTotalPontosKitAdesao());
        
        
        /*
         * Rede deste teste:
         *
                        luciano
                     /              \
                 diego            marcio
                   |
                 vera
         *
         */
        
        $luciano = new Cliente();
        $luciano->setNome('Luciano');
        $luciano->makeRoot();
        $luciano->save($con);
        
        $diego = new Cliente();
        $diego->setNome('Diego');
        $diego->insertAsFirstChildOf($luciano);
        $diego->setClienteRelatedByClienteIndicadorId($luciano);
        $diego->setClienteRelatedByClienteIndicadorDiretoId($luciano);
        $diego->save($con);
        
        $marcio = new Cliente();
        $marcio->setNome('Marcio Pedroso');
        $marcio->insertAsLastChildOf($luciano);
        $marcio->setClienteRelatedByClienteIndicadorId($luciano);
        $marcio->setClienteRelatedByClienteIndicadorDiretoId($luciano);
        $marcio->save($con);
        
        $vera = new Cliente();
        $vera->setNome('Vera');
        $vera->insertAsFirstChildOf($diego);
        $vera->setClienteRelatedByClienteIndicadorId($diego);
        $vera->setClienteRelatedByClienteIndicadorDiretoId($diego);
        $vera->save($con);
        
        //Luciano tem plano 4 (50% bonus rede binaria)
        $luciano->setPlano($plano4);
        $luciano->save($con);
                
        //diego faz um pedido e contrata o plano 3
        $pedido1 = new Pedido();
        $pedido1->setCliente($diego);
        $pedido1->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($kitPlano3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido1);
        $itemPedido->save($con);
        
        $gerenciador->distribuiPontosPedido($pedido1);
        $diego->setPlano($plano3);
        $diego->save($con);
        
        //vera faz um pedido e contrata o plano 4
        $pedido2 = new Pedido();
        $pedido2->setCliente($vera);
        $pedido2->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($kitPlano4->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido2);
        $itemPedido->save($con);
        
        $gerenciador->distribuiPontosPedido($pedido2);
        $vera->setPlano($plano4);
        $vera->save($con);
        
        //diego compra um purificador agua
        $pedido3 = new Pedido();
        $pedido3->setCliente($diego);
        $pedido3->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($purificador->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido3);
        $itemPedido->save($con);
        
        $gerenciador->distribuiPontosPedido($pedido3);
        
        //marcio tambem faz um pedido e contrata o plano 3
        $pedido4 = new Pedido();
        $pedido4->setCliente($marcio);
        $pedido4->save($con);
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($kitPlano3->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido4);
        $itemPedido->save($con);
        
        $gerenciador->distribuiPontosPedido($pedido4);
        $marcio->setPlano($plano3);
        $marcio->save($con);
                        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save($con);
        
        $gerenciador->geraPreview($distribuicao);
        
        // 120 = 50% (plano 4) de 240 (menor lado) [lado esquerdo: 240 + 360 + 180; lado direito: 240]
        $this->assertEquals(240, $this->getUltimaDistribuicaoDoCliente($distribuicao, $luciano)->getTotalPontosProcessados());
        $this->assertEquals(120, $this->getUltimaDistribuicaoDoCliente($distribuicao, $luciano)->getTotalPontos());
        
        //confirma
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        $this->assertEquals(120, $this->getUltimoExtratoCliente($distribuicao, $luciano)->getPontos());
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoCliente()
    {
        //Cria um teste com o exemplo que esta na documentacao
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
        
        $c11 = new Cliente();
        $c11->setNome('1.1');
        $c11->insertAsFirstChildOf($root);
        $c11->save($con);
        
        $c21 = new Cliente();
        $c21->setNome('2.1');
        $c21->insertAsFirstChildOf($c11);
        $c21->save($con);
        
        $c22 = new Cliente();
        $c22->setNome('2.2');
        $c22->insertAsLastChildOf($c11);
        $c22->save($con);
        
        $c12 = new Cliente();
        $c12->setNome('1.2');
        $c12->insertAsLastChildOf($root);
        $c12->save($con);
        
        $c23 = new Cliente();
        $c23->setNome('2.3');
        $c23->insertAsFirstChildOf($c12);
        $c23->save($con);
        
        $plano4 = new Plano();
        $plano4->setNome('Plano 4');
        $plano4->setRedeBinaria(50);
        $plano4->save($con);
        
        $root->setPlano($plano4);
        $root->save($con);
        
        //lado esquerdo (total 3540)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c11, 3000));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComKitAdesao($c21, 250, 250));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c22, 40));
        
        //lado direito (total 2150)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c12, 2000));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComKitAdesao($c23, 100, 50));
                
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save($con);
        
        $gerenciador->geraPreview($distribuicao);
        
        //deve ter sido gerado um registro de distribuicao_cliente com 2150 pontos
        $this->assertEquals(2150, $this->getUltimaDistribuicaoDoCliente($distribuicao, $root)->getTotalPontosProcessados());
        
        //ainda nao deve ter gerado extratos
        $this->assertNull($this->getUltimoExtratoCliente($distribuicao, $root));
        
        //confirma
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        //agora sim deve ter gerado um extrato com 1075 pontos para o cliente
        $this->assertEquals(1075, $this->getUltimoExtratoCliente($distribuicao, $root)->getPontos());
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoCliente2()
    {
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $cliente = $c11 = ClienteQuery::create()->findOneByNome('1.1');
        
        $plano = new Plano();
        $plano->setRedeBinaria(50);
        $cliente->setPlano($plano);
        $plano->save($con);
        $cliente->save($con);
        
        //lado esquerdo (total = 500)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.1'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 300));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 100));
        
        //lado direito (total = 300)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.2'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.3'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.4'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('4.1'), 50));
        
        $arrTotais = $gerenciador->getTotaisProximaDistribuicaoCliente($cliente);
        $this->assertEquals(500, $arrTotais['esquerda']);
        $this->assertEquals(300, $arrTotais['direita']);
        
        //testa outro cliente com os mesmos valores
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        $plano2 = new Plano();
        $plano2->setRedeBinaria(20);
        $plano2->save();
        $c22->setPlano($plano2);
        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save();
        
        $gerenciador->geraPreview($distribuicao);
        
        //cliente 1.1
        //Deve escolher o lado direito pois tem menos pontos (300).
        //Cliente deve receber 50% (por conta do plano) desses 300 (150).
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente);
        $this->assertEquals(300, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(150, $ultimaDistribuicao->getTotalPontos());
                
        //cliente 2.2
        //lado esquerdo 150
        //direito 100
        //distribui 20 (20% de 100)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $c22);
        $this->assertEquals(100, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(20, $ultimaDistribuicao->getTotalPontos());
                
        //confirma
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        $this->assertEquals(150, $this->getUltimoExtratoCliente($distribuicao, $cliente)->getPontos());
        $this->assertEquals(20, $this->getUltimoExtratoCliente($distribuicao, $c22)->getPontos());
        
        //*** segundo ciclo ****
        $distribuicao2 = new Distribuicao();
        $distribuicao2->setData(new DateTime("+2 seconds"));
        $distribuicao2->save();
        
        //lado esquerdo 350 (500 primeiro ciclo - 300 distribuidos no primeiro ciclo + 150 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.1'), 75));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 24));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 51));
        
        //lado direito 400 (300 primeiro cliclo - 300 distribuidos no primeiro ciclo + 400 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.2'), 150));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.3'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.4'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('4.1'), 100));
        
        $arrTotais = $gerenciador->getTotaisProximaDistribuicaoCliente($cliente);
        $this->assertEquals(350, $arrTotais['esquerda']);
        $this->assertEquals(400, $arrTotais['direita']);
                
        $gerenciador->geraPreview($distribuicao2);
        
        //cliente 1.1
        //Deve escolher o lado esquerdo (350 pontos)
        //Cliente deve receber um extrato com 175 (50% de 350)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao2, $cliente);
        $this->assertEquals(350, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(175, $ultimaDistribuicao->getTotalPontos());
        
        //cliente 2.2
        //esquerdo 200 (150 - 100 + 150)
        //direito 100 (100 - 100 + 100)
        //distribui 20 (20% 100)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao2, $c22);
        $this->assertEquals(100, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(20, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao2->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao2);
        
        $this->assertEquals(175, $this->getUltimoExtratoCliente($distribuicao2, $cliente)->getPontos());
        $this->assertEquals(20, $this->getUltimoExtratoCliente($distribuicao2, $c22)->getPontos());
        
        //**** terceiro ciclo ****
        $distribuicao3 = new Distribuicao();
        $distribuicao3->setData(new DateTime("+3 seconds"));
        $distribuicao3->save();
        
        //lado esquerdo 410 (500 primeiro ciclo + 150 segundo ciclo - 300 primeiro ciclo - 350 segundo ciclo + 410 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.1'), 300));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 10));
        
        //lado direito 430 (300 primeiro ciclo + 400 segundo ciclo - 300 primeiro ciclo - 350 segundo ciclo + 380 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.2'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.3'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.4'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('4.1'), 80));
        
        $arrTotais = $gerenciador->getTotaisProximaDistribuicaoCliente($cliente);
        $this->assertEquals(410, $arrTotais['esquerda']);
        $this->assertEquals(430, $arrTotais['direita']);
        
        $gerenciador->geraPreview($distribuicao3);
        
        //deve escolher o lado esquerdo 410
        //Cliente deve receber extrato com 205 (50% de 410)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao3, $cliente);
        $this->assertEquals(410, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(205, $ultimaDistribuicao->getTotalPontos());
        
        //cliente 2.2
        //esquerdo 280 (150 - 100 + 150 - 100 + 180)
        //direito 100 (100 - 100 + 100 - 100 + 100)
        //distribui 20 (20% 100)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao3, $c22);
        $this->assertEquals(100, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(20, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao3->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao3);
        
        $this->assertEquals(205, $this->getUltimoExtratoCliente($distribuicao3, $cliente)->getPontos());
        $this->assertEquals(20, $this->getUltimoExtratoCliente($distribuicao3, $c22)->getPontos());
        
        
        /*** quarto ciclo ****/
        $distribuicao4 = new Distribuicao();
        $distribuicao4->setData(new DateTime("+4 seconds"));
        $distribuicao4->save();
        
        //lado esquerdo 520 (500 primeiro + 150 segundo + 410 terceiro - 300 primeiro - 350 segundo - 410 terceiro + 520 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.1'), 500));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 15));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 5));
        
        //lado direito 510 (300 primeiro + 400 segundo + 380 terceiro - 300 primeiro - 350 segundo - 410 terceiro + 490 deste ciclo)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.2'), 200));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.3'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.4'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('4.1'), 190));
        
        $arrTotais = $gerenciador->getTotaisProximaDistribuicaoCliente($cliente);
        $this->assertEquals(520, $arrTotais['esquerda']);
        $this->assertEquals(510, $arrTotais['direita']);
        
        $gerenciador->geraPreview($distribuicao4);
        
        //deve escolher o lado direiro 510
        //cliente deve receber 255
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao4, $cliente);
        $this->assertEquals(510, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(255, $ultimaDistribuicao->getTotalPontos());
        
        //cliente 2.2
        //esquerdo 420 (150 - 100 + 150 - 100 + 180 - 100 + 240)
        //direito 50 (100 - 100 + 100 - 100 + 100 - 100 + 50)
        //distribui 10 (20% de 50)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao4, $c22);
        $this->assertEquals(50, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(10, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao4->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao4);
        
        $this->assertEquals(255, $this->getUltimoExtratoCliente($distribuicao4, $cliente)->getPontos());
        $this->assertEquals(10, $this->getUltimoExtratoCliente($distribuicao4, $c22)->getPontos());
                                
        
        /********** Testa outra parte da rede **********/
        $cliente2 = $c14 = ClienteQuery::create()->findOneByNome('1.4');
        
        $plano3 = new Plano();
        $plano3->setRedeBinaria(35);
        $cliente2->setPlano($plano3);
        $plano->save($con);
        $cliente2->save($con);
        
        //lado esquerdo (total 150)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 75));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 75));
        
        
        //lado direito (total 200)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.6'), 180));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.6'), 20));
        
        $distribuicao5 = new Distribuicao();
        $distribuicao5->setData(new DateTime('+5 seconds'));
        $distribuicao5->save($con);
                
        $gerenciador->geraPreview($distribuicao5);
        
        //52.5 = 35% de 150 pontos (menor lado)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao5, $cliente2);
        $this->assertEquals(52.5, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(150, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao5->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao5);
        
        $this->assertEquals(52.5, $this->getUltimoExtratoCliente($distribuicao5, $cliente2)->getPontos());
        
        //segundo ciclo
        
        //lado esquerdo 210 (150 - 150 + 210)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 210));
        
        //lado direito 205 (200 - 150 + 155)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.6'), 140));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.6'), 15));
        
        $distribuicao6 = new Distribuicao();
        $distribuicao6->setData(new DateTime('+6 seconds'));
        $distribuicao6->save($con);
        
        $gerenciador->geraPreview($distribuicao6);
        
        //205 menor lado
        //71,75 (35% de 205)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao6, $cliente2);
        $this->assertEquals(205, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(71.75, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao6->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao6);
        
        $this->assertEquals(71.75, $this->getUltimoExtratoCliente($distribuicao6, $cliente2)->getPontos());
        
        //terceiro ciclo
        
        //lado esquerdo 10 (150 - 150 + 210 - 205 + 5)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 5));
        
        //lado direto 15 (200 - 150 + 155 - 205 + 15)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.6'), 10));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.6'), 5));
        
        $distribuicao7 = new Distribuicao();
        $distribuicao7->setData(new DateTime('+7 seconds'));
        $distribuicao7->save($con);
        
        $gerenciador->geraPreview($distribuicao7);
        
        //10 menor lado
        //3,5 (35% de 10)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao7, $cliente2);
        $this->assertEquals(10, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(3.5, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao7->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao7);
        
        $this->assertEquals(3.5, $this->getUltimoExtratoCliente($distribuicao7, $cliente2)->getPontos());
        
        //quarto ciclo
        
        //lado esquerdo 800 (150 - 150 + 210 - 205 + 5 - 10 + 800)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 800));
        
        //lado direiro 795 (200 - 150 + 155 - 205 + 15 - 10 + 790)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.6'), 700));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.6'), 90));
        
        $distribuicao8 = new Distribuicao();
        $distribuicao8->setData(new DateTime('+8 seconds'));
        $distribuicao8->save($con);
        
        $gerenciador->geraPreview($distribuicao8);
        
        //795 menor lado
        //278,25 (35% de 795)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao8, $cliente2);
        $this->assertEquals(795, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(278.25, $ultimaDistribuicao->getTotalPontos());
        
        $distribuicao8->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao8);
        
        $this->assertEquals(278.25, $this->getUltimoExtratoCliente($distribuicao8, $cliente2)->getPontos());
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoCliente3()
    {
       //******
       // Verifica que se uma nova distribuição for executada sem que o lado menor receba novos pontos, nenhum extrato é gerado (pois o lado menor continua sendo menor/escolhido e esse tem 0 pontos)
       //
                
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setRedeBinaria(15);
        $plano->save($con);
        
        $cliente = ClienteQuery::create()->findOneByNome('1.4');
        $cliente->setPlano($plano);
        $cliente->save($con);
        
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.5'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.6'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.6'), 25));
        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save($con);
        
        $gerenciador->geraPreview($distribuicao);
        
        //esquerda (2.5): 100 pontos
        //direita (2.6 e 3.6) 75 pontos
        //recebe 11,25 (15% de 75)
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente);
        $this->assertEquals(11.25, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(75, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        $this->assertEquals(11.25, $this->getUltimoExtratoCliente($distribuicao, $cliente)->getPontos());
        
        $this->criaExtrato(ClienteQuery::create()->findOneByNome('2.5'), '+', 120, null, Extrato::TIPO_INDICACAO_INDIRETA);
        $this->criaExtrato(ClienteQuery::create()->findOneByNome('2.5'), '+', 30, null, Extrato::TIPO_RESIDUAL);
        
        $distribuicao2 = new Distribuicao();
        $distribuicao2->setData(new DateTime('+2 seconds'));
        $distribuicao2->save($con);
        
        $gerenciador->geraPreview($distribuicao2);
        
        //apenas o lado esquerdo (2.5) recebeu pontos. O lado direito (menor) não recebeu nenhum. Então retorna null.
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao2, $cliente);
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontosProcessados()); //nenhum ponto foi distribuido
        
        $distribuicao2->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao2);
        
        $this->assertNull($this->getUltimoExtratoCliente($distribuicao2, $cliente));
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoComMudancaPlano()
    {
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano1 = new Plano();
        $plano1->setRedeBinaria(20);
        $plano1->save($con);
        
        $plano2 = new Plano();
        $plano2->setRedeBinaria(35);
        $plano2->save($con);
        
        $cliente = ClienteQuery::create()->findOneByNome('2.1');
        $cliente->setPlano($plano1);
        $cliente->save($con);
        
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 150));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 100));
        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save($con);
        
        $gerenciador->geraPreview($distribuicao);
        
        //20% de 100
        $this->assertEquals(20, $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente)->getTotalPontos());
        
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        $this->assertEquals(20, $this->getUltimoExtratoCliente($distribuicao, $cliente)->getPontos());
                
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 120));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 100));
        
        //troca para o plano de 35%
        $cliente->setPlano($plano2);
        $cliente->save($con);
        
        $distribuicao2 = new Distribuicao();
        $distribuicao2->setData(new DateTime('+2 seconds'));
        $distribuicao2->save($con);
        
        $gerenciador->geraPreview($distribuicao2);
        
        //35% de 100
        $this->assertEquals(35, $this->getUltimaDistribuicaoDoCliente($distribuicao2, $cliente)->getTotalPontos());
        
        $distribuicao2->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao2);
        
        $this->assertEquals(35, $this->getUltimoExtratoCliente($distribuicao2, $cliente)->getPontos());
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoFilhosSemExtratos()
    {
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setRedeBinaria(30);
        $plano->save($con);
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->setPlano($plano);
        $root->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->setPlano($plano);
        $cliente1->insertAsFirstChildOf($root);
        $cliente1->save($con);
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->setPlano($plano);
        $cliente2->insertAsLastChildOf($root);
        $cliente2->save($con);
        
        $this->criaExtrato($root, '+', 100, null, Extrato::TIPO_INDICACAO_DIRETA);
        $this->criaExtrato($root, '+', 25, null, Extrato::TIPO_INDICACAO_INDIRETA);
                
        $distribuicao1 = new Distribuicao();
        $distribuicao1->setData(new DateTime());
        $distribuicao1->save($con);
        
        $gerenciador->geraPreview($distribuicao1);
        
        //nenhum filho de root possui extratos, logo um extrato de distribuicao nao deve ter sido gerado.
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao1, $root);
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao1->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao1);
        
        $this->assertNull($this->getUltimoExtratoCliente($distribuicao1, $root));
        
        $this->criaExtrato($cliente1, '-', 100, null, Extrato::TIPO_RESGATE);
        
        $this->criaExtrato($cliente2, '-', 50, null, Extrato::TIPO_SISTEMA);
        
        $distribuicao2 = new Distribuicao();
        $distribuicao2->setData(new DateTime('+2 seconds'));
        $distribuicao2->save($con);
        
        $gerenciador->geraPreview($distribuicao2);
        
        //agora os filhos possuem extratos, mas esses não dos tipos considerados pela distribuicao. Continua retornando null.
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao2, $root);
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao2->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao2);
        
        $this->assertNull($this->getUltimoExtratoCliente($distribuicao2, $root));
    }
    
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoClienteSemFilhos()
    {
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setRedeBinaria(25);
        $plano->save($con);
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->insertAsFirstChildOf($root);
        $cliente1->setPlano($plano);
        $cliente1->save($con);
        
        $this->criaExtrato($cliente1, '+', 100, null, Extrato::TIPO_INDICACAO_DIRETA);
        $this->criaExtrato($cliente1, '+', 50, null, Extrato::TIPO_INDICACAO_INDIRETA);
        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save($con);
        
        $gerenciador->geraPreview($distribuicao);
        
        //um registro de distribuicao_cliente deve ter sido gerada, mas com 0 pontos
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente1);
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        //como cliente 1 nao tem filhos, nenhum ponto foi gerado e nenhum extrato deve ter sido criado.
        $this->assertNull($this->getUltimoExtratoCliente($distribuicao, $cliente1));
        
        //testa outra distribuicao
        $distribuicao2 = new Distribuicao();
        $distribuicao2->setData(new DateTime('+1 second'));
        $distribuicao2->save($con);
        
        $gerenciador->geraPreview($distribuicao2);
        
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente1);
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontos());
        $this->assertEquals(0, $ultimaDistribuicao->getTotalPontosProcessados());
        
        $distribuicao2->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao2);

        $this->assertNull($this->getUltimoExtratoCliente($distribuicao2, $cliente1));
    }
    
    /**
     * @group distribuicao
     */
    public function testTotalizacaoPontosDistribuicao()
    {
        $this->criarRedeTeste();
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setRedeBinaria(10);
        $plano->save($con);
        
        $c11 = ClienteQuery::create()->findOneByNome('1.1');
        $c11->setPlano($plano);
        $c11->save($con);
        
        $c21 = ClienteQuery::create()->findOneByNome('2.1');
        $c21->setPlano($plano);
        $c21->save($con);
        
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        $c22->setPlano($plano);
        $c22->save($con);
        
        $c31 = ClienteQuery::create()->findOneByNome('3.1');
        $c31->setPlano($plano);
        $c31->save($con);
        
        $c32 = ClienteQuery::create()->findOneByNome('3.2');
        $c32->setPlano($plano);
        $c32->save($con);
        
        $c33 = ClienteQuery::create()->findOneByNome('3.3');
        $c33->setPlano($plano);
        $c33->save($con);
        
        $c34 = ClienteQuery::create()->findOneByNome('3.4');
        $c34->setPlano($plano);
        $c34->save($con);
        
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c21, 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c31, 10));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c32, 15));
        
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c22, 110));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c33, 20));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal($c34, 25));
        
        $distribuicao1 = new Distribuicao();
        $distribuicao1->setData(new DateTime());
        $distribuicao1->save($con);
        
        $gerenciador->geraPreview($distribuicao1);
        
        $this->assertEquals(12.5, $this->getUltimaDistribuicaoDoCliente($distribuicao1, $c11)->getTotalPontos());
        $this->assertEquals(1, $this->getUltimaDistribuicaoDoCliente($distribuicao1, $c21)->getTotalPontos());
        $this->assertEquals(2, $this->getUltimaDistribuicaoDoCliente($distribuicao1, $c22)->getTotalPontos());
        
        //deve ser zero porque ainda nao confirmou
        $this->assertEquals(0, $distribuicao1->getTotalPontos());
        
        $distribuicao1->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao1);
        
        $this->assertEquals(15.5, $distribuicao1->getTotalPontos());
    }
    
    /**
     * @group distribuicao
     */
    public function testDistribuicaoSemBonusPlano()
    {
        //Certifica que o patrocinador não recebe bonus se o plano dele não possuir este bonus
        
        $gerenciador = new GerenciadorBonusRedeBinaria($con = Propel::getConnection());
        
        $planoSemBonus = new Plano();
        $planoSemBonus->setRedeBinaria(0);
        $planoSemBonus->save($con);
        
        $planoComBunus = new Plano();
        $planoComBunus->setRedeBinaria(20);
        $planoComBunus->save($con);
        
        $this->criarRedeTeste();
                
        $cliente = $c11 = ClienteQuery::create()->findOneByNome('1.1');
        
        $cliente->setPlano($planoSemBonus);
        $cliente->save($con);
        
        //lado esquerdo (total = 500)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.1'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.1'), 300));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.2'), 100));
        
        //lado direito (total = 300)
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('2.2'), 50));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.3'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('3.4'), 100));
        $gerenciador->distribuiPontosPedido($this->criaPedidoComProdutoNormal(ClienteQuery::create()->findOneByNome('4.1'), 50));
        
        $arrTotais = $gerenciador->getTotaisProximaDistribuicaoCliente($cliente);
        $this->assertEquals(500, $arrTotais['esquerda']);
        $this->assertEquals(300, $arrTotais['direita']);
        
        //testa outro cliente com os mesmos valores
        $c22 = ClienteQuery::create()->findOneByNome('2.2');
        $c22->setPlano($planoComBunus);
        $c22->save($con);
        
        $distribuicao = new Distribuicao();
        $distribuicao->setData(new DateTime());
        $distribuicao->save();
        
        $gerenciador->geraPreview($distribuicao);
        
        //cliente 1.1
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $cliente);
        $this->assertNull($ultimaDistribuicao); //o plano deste cliente não concede bonus de rede binaria
                
        //cliente 2.2
        $ultimaDistribuicao = $this->getUltimaDistribuicaoDoCliente($distribuicao, $c22);
        $this->assertEquals(100, $ultimaDistribuicao->getTotalPontosProcessados());
        $this->assertEquals(20, $ultimaDistribuicao->getTotalPontos());
                
        //confirma
        $distribuicao->setStatus(Distribuicao::STATUS_AGUARDANDO);
        $gerenciador->confirmaDistribuicao($distribuicao);
        
        $this->assertEquals(20, $this->getUltimoExtratoCliente($distribuicao, $c22)->getPontos());
    }
    
    /**
     *
     * @param Distribuicao $distribuicao
     * @param Cliente $cliente
     * @return DistribuicaoCliente|null
     */
    protected function getUltimaDistribuicaoDoCliente(Distribuicao $distribuicao, Cliente $cliente)
    {
        return DistribuicaoClienteQuery::create()
                            ->filterByDistribuicao($distribuicao)
                            ->filterByCliente($cliente)
                            ->orderById(Criteria::DESC)
                            ->findOne();
    }
    
    
    /**
     *
     * @param Distribuicao $distribuicao
     * @param Cliente $cliente
     * @return Extrato|null
     */
    protected function getUltimoExtratoCliente(Distribuicao $distribuicao, Cliente $cliente)
    {
        return ExtratoQuery::create()
                        ->filterByDistribuicao($distribuicao)
                        ->filterByCliente($cliente)
                        ->filterByTipo(Extrato::TIPO_REDE_BINARIA)
                        ->filterByOperacao('+')
                        ->orderById(Criteria::DESC)
                        ->findOne();
    }


    /**
     *
     * @param Cliente $cliente
     * @param string $operacao
     * @param float $pontos
     * @param DateTime|null $data
     * @param string|null $tipo
     * @return \Extrato
     */
    protected function criaExtrato(Cliente $cliente, $operacao, $pontos, DateTime $data = null, $tipo = null)
    {
        if (null === $data) {
            $data = new Datetime();
        }
        if (null === $tipo) {
            $tipo = Extrato::TIPO_INDICACAO_DIRETA;
        }
        
        $extrato = new Extrato();
        $extrato->setCliente($cliente);
        $extrato->setOperacao($operacao);
        $extrato->setPontos($pontos);
        $extrato->setData($data);
        $extrato->setTipo($tipo);
        $extrato->save();
        
        return $extrato;
    }
    
    /**
     *
     * @param Cliente $cliente
     * @param float $valorPontos
     * @param float $valorBase
     * @return \Pedido
     */
    protected function criaPedidoComProdutoNormal(Cliente $cliente, $valorPontos, $valorBase = 100.0)
    {
        //cria um produto normal
        $produto = new Produto();
        $produto->setNome(uniqid('Produto '));
        $produto->getProdutoVariacao()->setValorBase($valorBase);
        $produto->setValorPontos($valorPontos);
        $produto->save();
                
        $pedido = new Pedido();
        $pedido->setCliente($cliente);
        $pedido->save();
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($produto->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido);
        $itemPedido->save();
        
        return $pedido;
    }
    
    /**
     *
     * @param Cliente $cliente
     * @param float $pontosProduto
     * @param float $pontosPlano
     * @param float $valorBase
     * @return \Pedido
     */
    protected function criaPedidoComKitAdesao(Cliente $cliente, $pontosProduto, $pontosPlano, $valorBase = 100.0)
    {
        $plano = new Plano();
        $plano->setNome(uniqid('Plano '));
        $plano->setGeracaoPontos($pontosPlano);
        $plano->save();
        
        $kitPlano = new Produto();
        $kitPlano->setPlanoRelatedByPlanoId($plano);
        $kitPlano->setNome('Kit adesão ' . $plano->getNome());
        $kitPlano->setValorPontos($pontosProduto);
        $kitPlano->getProdutoVariacao()->setValorBase($valorBase);
        $kitPlano->save();
        
        $pedido = new Pedido();
        $pedido->setCliente($cliente);
        $pedido->save();
        
        $itemPedido = new PedidoItem();
        $itemPedido->setProdutoVariacao($kitPlano->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido);
        $itemPedido->save();
        
        return $pedido;
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
    protected function criarRedeTeste()
    {
        $con = Propel::getConnection();
                
        //root
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
        
        //1.1
        $c11 = new Cliente();
        $c11->setNome('1.1');
        $c11->insertAsFirstChildOf($root);
        $c11->save($con);
        
        //1.2
        $c12 = new Cliente();
        $c12->setNome('1.2');
        $c12->insertAsLastChildOf($root);
        $c12->save($con);
        
        //1.3
        $c13 = new Cliente();
        $c13->setNome('1.3');
        $c13->insertAsLastChildOf($root);
        $c13->save($con);
        
        //1.4
        $c14 = new Cliente();
        $c14->setNome('1.4');
        $c14->insertAsLastChildOf($root);
        $c14->save($con);
        
        //2.1
        $c21 = new Cliente();
        $c21->setNome('2.1');
        $c21->insertAsFirstChildOf($c11);
        $c21->save($con);
        
        //2.2
        $c22 = new Cliente();
        $c22->setNome('2.2');
        $c22->insertAsLastChildOf($c11);
        $c22->save($con);
        
        //2.3
        $c23 = new Cliente();
        $c23->setNome('2.3');
        $c23->insertAsFirstChildOf($c12);
        $c23->save($con);
        
        //2.4
        $c24 = new Cliente();
        $c24->setNome('2.4');
        $c24->insertAsFirstChildOf($c13);
        $c24->save($con);
        
        //2.5
        $c25 = new Cliente();
        $c25->setNome('2.5');
        $c25->insertAsFirstChildOf($c14);
        $c25->save($con);
        
        //2.6
        $c26 = new Cliente();
        $c26->setNome('2.6');
        $c26->insertAsLastChildOf($c14);
        $c26->save($con);
        
        //3.1
        $c31 = new Cliente();
        $c31->setNome('3.1');
        $c31->insertAsFirstChildOf($c21);
        $c31->save($con);
        
        //3.2
        $c32 = new Cliente();
        $c32->setNome('3.2');
        $c32->insertAsLastChildOf($c21);
        $c32->save($con);
        
        //3.3
        $c33 = new Cliente();
        $c33->setNome('3.3');
        $c33->insertAsFirstChildOf($c22);
        $c33->save($con);
        
        //3.4
        $c34 = new Cliente();
        $c34->setNome('3.4');
        $c34->insertAsLastChildOf($c22);
        $c34->save($con);
        
        //3.5
        $c35 = new Cliente();
        $c35->setNome('3.5');
        $c35->insertAsFirstChildOf($c24);
        $c35->save($con);
        
        //3.6
        $c36 = new Cliente();
        $c36->setNome('3.6');
        $c36->insertAsFirstChildOf($c26);
        $c36->save($con);
        
        //4.1
        $c41 = new Cliente();
        $c41->setNome('4.1');
        $c41->insertAsFirstChildOf($c33);
        $c41->save($con);
        
        //4.2
        $c42 = new Cliente();
        $c42->setNome('4.2');
        $c42->insertAsFirstChildOf($c35);
        $c42->save($con);
    }
}

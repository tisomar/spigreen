<?php

namespace App\Tests;

require_once 'include_propel_tests.inc.php';

use Cliente;
use ClientePeer;
use ClienteQuery;
use Extrato;
use ExtratoQuery;
use GerenciadorPontos;
use GerenciadorRede;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pedido;
use PedidoItem;
use PedidoPeer;
use PHPUnit\Framework\TestCase;
use Plano;
use Produto;
use ProdutoQuery;
use ProdutoVariacaoQuery;
use Propel;
use PropelPDO;

/**
 * Class GerenciadorPontosTest
 *
 * @package App\Tests
 * @author Jesse Quinn
 */
class GerenciadorPontosTest extends TestCase
{
    /** @var array garbage collector */
    private static $collector = array();

    /**
     * Creates client based on SPIGREEN sponsor.
     *
     * @return Cliente
     * @throws \PropelException
     */
    public function testCriarClienteDefaultPatrocinador()
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));
        $gerenciadorRede = new GerenciadorRede($con = Propel::getConnection(), $logger);
        $cliente = new Cliente();
        $cliente->setNomeFantasia('Dummy Dummy');
        $cliente->setNome('Dummy Dummy');
        $cliente->save($con);
        $patrocinador = $gerenciadorRede->insereRede($cliente);
        $this->assertEquals(1, ClienteQuery::create()->findById($cliente->getId())->count());
        $this->assertEquals(48283077851, $patrocinador->getChaveIndicacao()); // SPIGREEN
        array_push(self::$collector, $cliente);

        return $cliente;
    }

    /**
     * Creates client based on the above client.
     *
     * @depends testCriarClienteDefaultPatrocinador
     * @param Cliente $cliente
     * @return Cliente
     * @throws \PropelException
     */
    public function testCriarClientePatrocinador(Cliente $cliente)
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));
        $patrocinadorClientePeer = ClientePeer::retrieveByPK($cliente->getId());
        $gerenciadorRede =
            new GerenciadorRede($con = Propel::getConnection(), $logger); // TODO: update source code to pass Logger
        $cliente = new Cliente();
        $cliente->setNomeFantasia('Dummy Dummy');
        $cliente->setNome('Dummy Dummy');
        $cliente->save($con);
        $patrocinador = $gerenciadorRede->insereRede($cliente, $patrocinadorClientePeer);
        $this->assertEquals($patrocinadorClientePeer->getChaveIndicacao(), $patrocinador->getChaveIndicacao());
        array_push(self::$collector, $cliente);

        return $cliente;
    }

    /**
     * Generate both indicacao indireta and direta.
     *
     * @depends testCriarClientePatrocinador
     * @throws \PropelException
     * @throws \Exception
     */
    public function testIndicacaoDireta(Cliente $cliente)
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));
        $gerenciadorPontos =
            new GerenciadorPontos($con = Propel::getConnection(), $logger);
        $kitAdesao = ProdutoQuery::create()->findPk(123);
        $this->assertEquals(123, $kitAdesao->getId());
        $kitAdesao2 = ProdutoVariacaoQuery::create()->findPk(126);
        $this->assertEquals(126, $kitAdesao2->getId());
        $pedido = new Pedido();
        $pedido->setCliente($cliente);
        $pedido->save($con);
        $itemPedido = new PedidoItem();
        $itemPedido->setPeso($kitAdesao->getPeso());
        $itemPedido->setValorUnitario($kitAdesao2->getValorPromocional());
        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
        $itemPedido->setQuantidade(1);
        $itemPedido->setPedido($pedido);
        $itemPedido->save($con);
        $pedido->calculateItemsValorTotal();
        $pedido->save($con);
        $gerenciadorPontos->distribuiPontosPedido($pedido);
        $this->assertEquals(
            29.4, // level 3 - 5%
            $this->somaPontosCliente($cliente->getPatrocinador(), Extrato::TIPO_INDICACAO_DIRETA, $con)
        );
        array_push(self::$collector, $pedido, $itemPedido);
    }

    /**
     * Generate seven additional accounts.
     *
     * @depends testCriarClienteDefaultPatrocinador
     * @param Cliente $cliente
     * @return Cliente
     * @throws \PropelException
     */
    public function testCriarSeteNiveis(Cliente $cliente)
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));

        for ($i = 1; $i <= 7; $i++) :
            $patrocinadorClientePeer = ClientePeer::retrieveByPK($cliente->getId());
            $gerenciadorRede =
                new GerenciadorRede($con = Propel::getConnection(), $logger); // TODO: update source code to pass Logger
            $cliente = new Cliente();
            $cliente->setNomeFantasia('Dummy Dummy ' . $i);
            $cliente->setNome('Dummy Dummy ' . $i);
            $cliente->save($con);
            $patrocinador = $gerenciadorRede->insereRede($cliente, $patrocinadorClientePeer);
            $this->assertEquals($patrocinadorClientePeer->getChaveIndicacao(), $patrocinador->getChaveIndicacao());
            array_push(self::$collector, $cliente);
        endfor;

        return $cliente;
    }

    /**
     * Generate the indicacao indireta for each sponsor.
     *
     * @depends testCriarSeteNiveis
     * @param Cliente $cliente
     * @throws \PropelException
     */
    public function testIndicacaoIndireta(Cliente $cliente)
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));
        $gerenciadorPontos =
            new GerenciadorPontos($con = Propel::getConnection(), $logger); // TODO: update source code to pass Logger
        $kitAdesao = ProdutoQuery::create()->findPk(123);
        $this->assertEquals(123, $kitAdesao->getId());
        $kitAdesao2 = ProdutoVariacaoQuery::create()->findPk(126);
        $this->assertEquals(126, $kitAdesao2->getId());
        $pedido = new Pedido();
        $pedido->setCliente($cliente);
        $pedido->save($con);
        $itemPedido = new PedidoItem();
        $itemPedido->setPeso($kitAdesao->getPeso());
        $itemPedido->setValorUnitario($kitAdesao2->getValorPromocional());
        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
        $itemPedido->setQuantidade(3);
        $itemPedido->setPedido($pedido);
        $itemPedido->save($con);
        $pedido->calculateItemsValorTotal();
        $pedido->save($con);
        $gerenciadorPontos->distribuiPontosPedido($pedido);
        $this->assertEquals(
            0, // outside of 7 generations - receives 0%
            $this->somaPontosCliente($cliente->getPatrocinador(), Extrato::TIPO_INDICACAO_INDIRETA, $con)
        );
        array_push(self::$collector, $pedido, $itemPedido);
    }

    /** Iterates through a collector of Propel objects and prints the stored values in a log file for future removal. */
    public function tearDown(): void
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));

        foreach (self::$collector as $object) :
            $logger->info(get_class($object) . ' : ' . $object->getId());
        endforeach;
    }

//    public function testIndicacaoDiretaUpgradePlano()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        //cria um plano que concede bonus de indicacao direta para o patrocinador
//        $planoIndicacaoDireta = new Plano();
//        $planoIndicacaoDireta->setIndicacaoDireta(true);
//        $planoIndicacaoDireta->save($con);
//
//        $patrocinador = new Cliente();
//        $patrocinador->makeRoot();
//        $patrocinador->setPlano($planoIndicacaoDireta);
//        $patrocinador->save($con);
//
//        $cliente = new Cliente();
//        $cliente->insertAsFirstChildOf($patrocinador);
//        $cliente->setClienteRelatedByClienteIndicadorId($patrocinador);
//        $cliente->save($con);
//
//        $plano1 = new Plano();
//        $plano1->setGeracaoPontos(100);
//        $plano1->save($con);
//
//        $kitAdesao1 = new Produto();
//        $kitAdesao1->setValorPontos(20);
//        $kitAdesao1->setPlanoRelatedByPlanoId($plano1);
//        $kitAdesao1->save($con);
//
//        $pedido1 = new Pedido();
//        $pedido1->setCliente($cliente);
//        $pedido1->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao1->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido1);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido1);
//
//        $this->assertEquals(24, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_DIRETA));
//
//        //faz upgrade para um plano maior
//        $plano2 = new Plano();
//        $plano2->setGeracaoPontos(200);
//        $plano2->save($con);
//
//        $kitAdesao2 = new Produto();
//        $kitAdesao2->setValorPontos(40);
//        $kitAdesao2->setPlanoRelatedByPlanoId($plano2);
//        $kitAdesao2->save($con);
//
//        $pedido2 = new Pedido();
//        $pedido2->setCliente($cliente);
//        $pedido2->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao2->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido2);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido2);
//
//        //O total a distribuir seria 48 (20% de 240), mas temos que descontar o que ja foi distribuido na compra do plano 1. Portanto, o patrocinador deve receber 24 (48 deste pedido - 24 do pedido 1)
//        $query = ExtratoQuery::create()
//                        ->filterByCliente($patrocinador)
//                        ->filterByTipo(Extrato::TIPO_INDICACAO_DIRETA)
//                        ->filterByPedido($pedido2);
//        $this->assertEquals(24, $query->findOne()->getPontos());
//
//        //outro upgrade
//        $plano3 = new Plano();
//        $plano3->setGeracaoPontos(300);
//        $plano3->save($con);
//
//        $kitAdesao3 = new Produto();
//        $kitAdesao3->setValorPontos(60);
//        $kitAdesao3->setPlanoRelatedByPlanoId($plano3);
//        $kitAdesao3->save($con);
//
//        $pedido3 = new Pedido();
//        $pedido3->setCliente($cliente);
//        $pedido3->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao3->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido3);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido3);
//
//        //o total a distribuir seria 72 (20% 360), mas temos que descontar o que ja foi distribuido na compra dos planos 1 e 2. Portanto, o patrocinador deve receber 24 (72 deste pedido - 24 pedido 1 - 24 pedido 2)
//        $query = ExtratoQuery::create()
//                        ->filterByCliente($patrocinador)
//                        ->filterByTipo(Extrato::TIPO_INDICACAO_DIRETA)
//                        ->filterByPedido($pedido3);
//        $this->assertEquals(24, $query->findOne()->getPontos());
//
//        //todos pedidos
//        $this->assertEquals(72, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_DIRETA));
//    }
//
//    public function testIndicacaoDiretaPatrocinadorDiferenteSolicitado()
//    {
//        /*
//         * Testa o caso onde o cliente é inserido abaixo de um patrocinador diferente do solicitado.
//         * Neste caso, quem recebe o bonus de indicação direta é o patrocinador solicitado e não patrocinador "pai" da arvore.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $gerenciadorRede = new GerenciadorRede($con);
//
//        //cria um plano que concede bonus de indicacao direta para o patrocinador
//        $planoIndicacaoDireta = new Plano();
//        $planoIndicacaoDireta->setIndicacaoDireta(true);
//        $planoIndicacaoDireta->save($con);
//
//        $root = new Cliente();
//        $root->setNome('root');
//        $root->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($root, null, false);
//
//        $c11 = new Cliente();
//        $c11->setNome('1.1');
//        $c11->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($c11, $root, false);
//
//        $c12 = new Cliente();
//        $c12->setNome('1.2');
//        $c12->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($c12, $root, false);
//
//        $c21 = new Cliente();
//        $c21->setNome('2.1');
//        $c21->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($c21, $c11, false);
//
//        $c22 = new Cliente();
//        $c22->setNome('2.2');
//        $c22->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($c22, $c11, false);
//
//        $c31 = $cliente = new Cliente();
//        $c31->setNome('3.1');
//        $c31->setPlano($planoIndicacaoDireta);
//        $gerenciadorRede->insereRede($c31, $c11, false);
//
//        //3.1 solicitou como patrocinador 1.1, mas como esse ja tem dois filhos, foi inserido como filho de 2.1
//        $this->assertEquals('2.1', $c31->getPatrocinador($con)->getNome());
//
//        //mas o patrocinador "direto" ficou mesmo sendo o 1.1
//        $this->assertEquals('1.1', $c31->getPatrocinadorDireto($con)->getNome());
//
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(120);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(80);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($c31);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //quem deve receber o bonus de indicao direta é o patrocinador 1.1
//        //2.1 que é o pai de 3.1 na arvore binaria, não deve receber este bonus
//        $this->assertEquals(40, $this->somaPontosCliente($c11, Extrato::TIPO_INDICACAO_DIRETA, $con));
//        $this->assertEquals(0, $this->somaPontosCliente($c21, Extrato::TIPO_INDICACAO_DIRETA, $con));
//    }
//
//    public function testIndicacaoDiretaSemBonusNoPlano()
//    {
//        /*
//         * Verifica que o patrocinador não recebe o bonus de indicacao direta quando o seu plano não possuir este bonus.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $plano1 = new Plano();
//        $plano1->setIndicacaoDireta(false); //este plano nao da bonus de indicacao direta
//        $plano1->save();
//
//        $patrocinador = new Cliente();
//        $patrocinador->makeRoot();
//        $patrocinador->setPlano($plano1);
//        $patrocinador->save($con);
//
//        $cliente = new Cliente();
//        $cliente->insertAsFirstChildOf($patrocinador);
//        $cliente->setClienteRelatedByClienteIndicadorId($patrocinador);
//        $cliente->save($con);
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(120);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(80);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //O patrocinador deveria possuir um extrato com 40 pontos, mas seu plano não da bonus de indicacao direta. Portanto ele possui 0 pontos.
//        $this->assertEquals(0, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_DIRETA, $con));
//    }
//
//
//    public function testIndicacaoIndireta()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.1');
//
//        //cria um plano que concede bonus de indicacao indireta para o patrocinadores
//        $planoIndicacaoIndireta = new Plano();
//        $planoIndicacaoIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoIndireta->save($con);
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoIndicacaoIndireta);
//            $patrocinador->save($con);
//        }
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 30 produto + 90 plano
//        $this->assertEquals(120, $kitAdesao->getTotalPontosKitAdesao());
//
//        $produtoNormal = new Produto();
//        $produtoNormal->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoNormal->getProdutoVariacao());
//        $itemPedido2->setQuantidade(2);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        $patrocinador = $cliente->getParent($con);
//
//        //o patrocinador direto nao deve receber, pois ja recebeu na indicacao direta
//        $this->assertEquals(0, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//        //verifica se os 10 niveis acima do patrocinador direto receberam 3,6 pontos (3% de 120)
//        $patrocinador = $patrocinador->getParent($con);
//        for ($i = 0; $i < Extrato::QUANTIDADE_NIVEIS_BONUS_INDICACAO_INDIRETA; $i++) {
//            $this->assertEquals(3.6, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//            $patrocinador = $patrocinador->getParent($con);
//            if (!$patrocinador) {
//                break;
//            }
//        }
//
//        //O 11º acima, no caso cliente "3.1", não deve ter recebido pontos.
//        $this->assertEquals(0, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('3.1')));
//
//        /*** verifica que se invocarmos a rotina de distribuição novamente os pontos não são duplicados ***/
//        $gerenciador->distribuiPontosPedido($pedido);
//        $this->assertEquals(3.6, $this->somaPontosCliente($cliente->getPatrocinador($con)->getPatrocinador($con), Extrato::TIPO_INDICACAO_INDIRETA, $con));
//    }
//
//
//    public function testIndicacaoIndiretaUpgradePlano()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        //cria um plano que concede bonus de indicacao indireta para o patrocinadores
//        $planoIndicacaoIndireta = new Plano();
//        $planoIndicacaoIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoIndireta->save($con);
//
//        $cliente = $c31 = ClienteQuery::create()->findOneByNome('3.1');
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoIndicacaoIndireta);
//            $patrocinador->save($con);
//        }
//
//        $plano1 = new Plano();
//        $plano1->setGeracaoPontos(100);
//        $plano1->save($con);
//
//        $kitAdesao1 = new Produto();
//        $kitAdesao1->setValorPontos(20);
//        $kitAdesao1->setPlanoRelatedByPlanoId($plano1);
//        $kitAdesao1->save($con);
//
//        $pedido1 = new Pedido();
//        $pedido1->setCliente($cliente);
//        $pedido1->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao1->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido1);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido1);
//
//        //patrocinadores indiretos recebem 3.6 (3% de 120)
//        $this->assertEquals(3.6, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('1.1'), Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(3.6, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('root'), Extrato::TIPO_INDICACAO_INDIRETA));
//
//        //faz upgrade de plano
//        $plano2 = new Plano();
//        $plano2->setGeracaoPontos(200);
//        $plano2->save($con);
//
//        $kitAdesao2 = new Produto();
//        $kitAdesao2->setValorPontos(40);
//        $kitAdesao2->setPlanoRelatedByPlanoId($plano2);
//        $kitAdesao2->save($con);
//
//        $pedido2 = new Pedido();
//        $pedido2->setCliente($cliente);
//        $pedido2->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao2->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido2);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido2);
//
//        //deveria ser 7.2 pontos (3% de 240), mas devemos subtrair os pontos ja distribuidos no no plano 1, entao fica 3.6 (3% de 120 [240 pedido2 - 120 pedido1])
//        $this->assertEquals(3.6, $this->somaPontosPatrocinadorNoPedido(ClienteQuery::create()->findOneByNome('1.1'), $pedido2, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(3.6, $this->somaPontosPatrocinadorNoPedido(ClienteQuery::create()->findOneByNome('root'), $pedido2, Extrato::TIPO_INDICACAO_INDIRETA));
//
//        //faz upgrade de plano
//        $plano3 = new Plano();
//        $plano3->setGeracaoPontos(300);
//        $plano3->save($con);
//
//        $kitAdesao3 = new Produto();
//        $kitAdesao3->setValorPontos(60);
//        $kitAdesao3->setPlanoRelatedByPlanoId($plano3);
//        $kitAdesao3->save($con);
//
//        $pedido3 = new Pedido();
//        $pedido3->setCliente($cliente);
//        $pedido3->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao3->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido3);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido3);
//
//        //deveria ser 10.8 (3% de 360), mas devemos subtratir os pontos ja distribuidos no plano 1 e plano 2, então fica 3.6 (3% de 120) [360 plano 3 - 120 pedido1 - 120 pedido2]
//        $this->assertEquals(3.6, $this->somaPontosPatrocinadorNoPedido(ClienteQuery::create()->findOneByNome('1.1'), $pedido3, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(3.6, $this->somaPontosPatrocinadorNoPedido(ClienteQuery::create()->findOneByNome('root'), $pedido3, Extrato::TIPO_INDICACAO_INDIRETA));
//    }
//
//
//    /**
//     * Testa com um cliente em um nivel menor que 10 e com valores diferentes do teste acima.
//     *
//     */
//    public function testIndicacaoIndireta2()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        //cria um plano que concede bonus de indicacao indireta para o patrocinadores
//        $planoIndicacaoIndireta = new Plano();
//        $planoIndicacaoIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoIndireta->save($con);
//
//        $cliente = ClienteQuery::create()->findOneByNome('3.2');
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoIndicacaoIndireta);
//            $patrocinador->save($con);
//        }
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(100);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(50);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 50 produto + 100 plano
//        $this->assertEquals(150, $kitAdesao->getTotalPontosKitAdesao());
//
//        $produtoNormal = new Produto();
//        $produtoNormal->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoNormal->getProdutoVariacao());
//        $itemPedido2->setQuantidade(2);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        $this->assertEquals(4.5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('1.2'), Extrato::TIPO_INDICACAO_INDIRETA, $con));
//        $this->assertEquals(4.5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('root'), Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//        //garante que o cliente "filho" (4.2) não recebeu pontos
//        $this->assertEquals(0.0, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('4.2'), Extrato::TIPO_INDICACAO_INDIRETA, $con));
//    }
//
//    public function testIndicacaoIndiretaSemBonusNoPlano()
//    {
//        /*
//         * Verifica que os patrocinadores não recebem o bonus de indicacao indireta quando seus planos não possuirem este bonus.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $planoSemIndicacaoIndireta = new Plano();
//        $planoSemIndicacaoIndireta->setIndicacaoIndireta(false); //plano sem indicacao indireta
//        $planoSemIndicacaoIndireta->save($con);
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.1');
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoSemIndicacaoIndireta);
//            $patrocinador->save($con);
//        }
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 30 produto + 90 plano
//        $this->assertEquals(120, $kitAdesao->getTotalPontosKitAdesao());
//
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        $patrocinador = $cliente->getParent($con);
//
//        //o patrocinador direto nao deve receber, pois ja recebeu na indicacao direta
//        $this->assertEquals(0, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//        //verifica se os 10 niveis acima do patrocinador direto receberam 3,6 pontos (3% de 120)
//        $patrocinador = $patrocinador->getParent($con);
//        for ($i = 0; $i < Extrato::QUANTIDADE_NIVEIS_BONUS_INDICACAO_INDIRETA; $i++) {
//            //deveriam receber 3.6 pontos, mas como seus planos não concedem bonus de indicacao indireta, ficam com 0.
//            $this->assertEquals(0, $this->somaPontosCliente($patrocinador, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//            $patrocinador = $patrocinador->getParent($con);
//            if (!$patrocinador) {
//                break;
//            }
//        }
//    }
//
//
//    public function testIndicacaoIndiretaHotfix()
//    {
//        //Faz um teste como um exemplo que o cliente enviou como ajuste
//
//        //arvore:
//        //                    c1
//        //                /        \
//        //               c2        c3
//        //               |
//        //               |
//        //              c4: (direto c1)
//
//        // c4 foi inserido abaixo de c2, mas seu indicador direto é c1.
//        // c1 deve receber bonus de indicacao direta, mas não de indireta (esse é o bug informado pelo cliente, e o que este test vai testar)
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $planoIndicacaoDiretaIndireta = new Plano();
//        $planoIndicacaoDiretaIndireta->setIndicacaoDireta(true);
//        $planoIndicacaoDiretaIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoDiretaIndireta->save($con);
//
//        $c1 = new Cliente();
//        $c1->setNome('c1');
//        $c1->makeRoot();
//        $c1->setPlano($planoIndicacaoDiretaIndireta);
//        $c1->save($con);
//
//        $c2 = new Cliente();
//        $c2->setNome('c2');
//        $c2->insertAsFirstChildOf($c1);
//        $c2->setClienteRelatedByClienteIndicadorId($c1);
//        $c2->setClienteRelatedByClienteIndicadorDiretoId($c1);
//        $c2->setPlano($planoIndicacaoDiretaIndireta);
//        $c2->save($con);
//
//        $c3 = new Cliente();
//        $c3->setNome('c3');
//        $c3->insertAsLastChildOf($c1);
//        $c3->setClienteRelatedByClienteIndicadorId($c1);
//        $c3->setClienteRelatedByClienteIndicadorDiretoId($c1);
//        $c3->setPlano($planoIndicacaoDiretaIndireta);
//        $c3->save($con);
//
//        $c4 = new Cliente();
//        $c4->setNome('c4');
//        $c4->insertAsFirstChildOf($c2);
//        $c4->setClienteRelatedByClienteIndicadorId($c2);
//        $c4->setClienteRelatedByClienteIndicadorDiretoId($c1);
//        $c4->setPlano($planoIndicacaoDiretaIndireta);
//        $c4->save($con);
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 30 produto + 90 plano
//        $this->assertEquals(120, $kitAdesao->getTotalPontosKitAdesao());
//
//
//        $pedido = new Pedido();
//        $pedido->setCliente($c4);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //c2 recebe 3,6 pontos (3% de 120)
//        ////ajuste: na verdade também não deve receber. Tem que subir a rede atraves do indicador direto. c2 não e ancestral "direto" de c4
//        //$this->assertEquals(3.6, $this->somaPontosCliente($c2, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//        $this->assertEquals(0, $this->somaPontosCliente($c2, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//
//        //c1 não deve receber, pois ja recebeu bonus de indicacao direta
//        $this->assertEquals(0, $this->somaPontosCliente($c1, Extrato::TIPO_INDICACAO_INDIRETA, $con));
//    }
//
//
//    public function testIndicacaoIndiretaHotfix2()
//    {
//        /*
//         * Verifica que a distribuicao indireta distribui os pontos subindo na arvore atraves do patrocinador direto e não traves do pai na arvore, como tinha sido programado inicialmente.
//         *
//         *
//         */
//
//        /*
//         * Arvore:
//         *
//         *                       c1
//         *                     /    \
//         *                    c2    c3 (direto c1)
//         *                   /        \
//         *                  c4        c5
//         *                 /            \
//         *   (direto c7)  c6             c7 (direto c3)
//         *
//         * O pedido será feito por c6. Quem deve receber os pontos são c7, c3 e c1.
//         * c4 e c2 são ancestrais na arvore de c6, mas não são patrocinadores diretos.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $planoIndicacaoDiretaIndireta = new Plano();
//        $planoIndicacaoDiretaIndireta->setIndicacaoDireta(true);
//        $planoIndicacaoDiretaIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoDiretaIndireta->save($con);
//
//        $c1 = new Cliente();
//        $c1->setNome('c1');
//        $c1->setPlano($planoIndicacaoDiretaIndireta);
//        $c1->makeRoot();
//        $c1->save($con);
//
//        $c2 = new Cliente();
//        $c2->setNome('c2');
//        $c2->setPlano($planoIndicacaoDiretaIndireta);
//        $c2->insertAsFirstChildOf($c1);
//        $c2->save($con);
//
//        $c3 = new Cliente();
//        $c3->setNome('c3');
//        $c3->setPlano($planoIndicacaoDiretaIndireta);
//        $c3->insertAsLastChildOf($c1);
//        $c3->save($con);
//
//        $c4 = new Cliente();
//        $c4->setNome('c4');
//        $c4->setPlano($planoIndicacaoDiretaIndireta);
//        $c4->insertAsFirstChildOf($c2);
//        $c4->save($con);
//
//        $c5 = new Cliente();
//        $c5->setNome('c5');
//        $c5->setPlano($planoIndicacaoDiretaIndireta);
//        $c5->insertAsFirstChildOf($c3);
//        $c5->save($con);
//
//        $c6 = new Cliente();
//        $c6->setNome('c6');
//        $c6->setPlano($planoIndicacaoDiretaIndireta);
//        $c6->insertAsFirstChildOf($c4);
//        $c6->save($con);
//
//        $c7 = new Cliente();
//        $c7->setNome('c7');
//        $c7->setPlano($planoIndicacaoDiretaIndireta);
//        $c7->insertAsFirstChildOf($c5);
//        $c7->save($con);
//
//        //patrocinadores diretos
//        $c6->setClienteRelatedByClienteIndicadorDiretoId($c7);
//        $c6->save($con);
//
//        $c7->setClienteRelatedByClienteIndicadorDiretoId($c3);
//        $c7->save($con);
//
//        $c3->setClienteRelatedByClienteIndicadorDiretoId($c1);
//        $c3->save($con);
//
//
//        // pedido
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 30 produto + 90 plano
//        $this->assertEquals(120, $kitAdesao->getTotalPontosKitAdesao());
//
//        $pedido = new Pedido();
//        $pedido->setCliente($c6);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //esses nao devem receber
//        $this->assertEquals(0, $this->somaPontosCliente($c4, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(0, $this->somaPontosCliente($c2, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(0, $this->somaPontosCliente($c5, Extrato::TIPO_INDICACAO_INDIRETA));
//
//        //c7 deve receber de indicacao direta
//        $this->assertEquals(0, $this->somaPontosCliente($c7, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertNotEquals(0, $this->somaPontosCliente($c7, Extrato::TIPO_INDICACAO_DIRETA));
//
//        //esses devem
//        $this->assertEquals(3.6, $this->somaPontosCliente($c3, Extrato::TIPO_INDICACAO_INDIRETA));
//        $this->assertEquals(3.6, $this->somaPontosCliente($c1, Extrato::TIPO_INDICACAO_INDIRETA));
//    }
//
//
//    /**
//     * Faz um teste que verifica se o patrocinador recebeu os pontos das indicações diretas e indiretas.
//     */
//    public function testIndicacaoDiretaIndireta()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.1');
//
//        //cria um plano que concede bonus de indicacao direta e indireta para o patrocinador
//        $planoIndicacaoDiretaIndireta = new Plano();
//        $planoIndicacaoDiretaIndireta->setIndicacaoDireta(true);
//        $planoIndicacaoDiretaIndireta->setIndicacaoIndireta(true);
//        $planoIndicacaoDiretaIndireta->save($con);
//
//        $cliente->getPatrocinador()->setPlano($planoIndicacaoDiretaIndireta);
//        $cliente->getPatrocinador()->save($con);
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(150);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(50);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        // 50 produto + 150 plano
//        $this->assertEquals(200, $kitAdesao->getTotalPontosKitAdesao());
//
//        $produtoNormal = new Produto();
//        $produtoNormal->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoNormal->getProdutoVariacao());
//        $itemPedido2->setQuantidade(2);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        $patrocinador = $cliente->getPatrocinador($con);
//
//        //40 pontos = 40 indicação direta + 0 indicação indireta (ajuste: não deve receber da indireta)
//        $this->assertEquals(40, $this->somaPontosCliente($patrocinador, null, $con));
//    }
//
//    public function testBonusResidual()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.1');
//
//        //cria um plano que concede bonus residual para os patrocinadores
//        $planoResidual = new Plano();
//        $planoResidual->setResidual(true);
//        $planoResidual->save($con);
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoResidual);
//            $patrocinador->save($con);
//        }
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//
//        $produtoNormal = new Produto();
//        $produtoNormal->setValorPontos(120);
//        $produtoNormal->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        /* nao devera ser considerado (kits nao distribuem bonus residual */
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoNormal->getProdutoVariacao());
//        $itemPedido2->setQuantidade(1);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //nivel 1 a 4, 5% (6 pontos)
//        $arr = array(
//            ClienteQuery::create()->findOneByNome('14.1'),
//            ClienteQuery::create()->findOneByNome('13.1'),
//            ClienteQuery::create()->findOneByNome('12.1'),
//            ClienteQuery::create()->findOneByNome('11.1')
//        );
//        foreach ($arr as $patrocinador) {
//            $this->assertEquals(6, $this->somaPontosCliente($patrocinador, Extrato::TIPO_RESIDUAL, $con));
//        }
//
//        //5 a 7, 10%
//        $arr = array(
//            ClienteQuery::create()->findOneByNome('10.1'),
//            ClienteQuery::create()->findOneByNome('9.1'),
//            ClienteQuery::create()->findOneByNome('8.1')
//        );
//        foreach ($arr as $patrocinador) {
//            $this->assertEquals(12, $this->somaPontosCliente($patrocinador, Extrato::TIPO_RESIDUAL, $con));
//        }
//
//        //testa que o oitavo nivel (7.1) não recebeu pontos
//        $this->assertEquals(0, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('7.1'), Extrato::TIPO_RESIDUAL, $con));
//
//        /*** verifica que se invocarmos a rotina de distribuição novamente os pontos não são duplicados ***/
//        $gerenciador->distribuiPontosPedido($pedido);
//        $this->assertEquals(12, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('10.1'), Extrato::TIPO_RESIDUAL, $con));
//    }
//
//
//    /**
//     * Faz um teste com um pedido com varios produtos, com quantidades difentes de 1.
//     */
//    public function testBonusResidual2()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.2');
//
//        //cria um plano que concede bonus residual para os patrocinadores
//        $planoResidual = new Plano();
//        $planoResidual->setResidual(true);
//        $planoResidual->save($con);
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoResidual);
//            $patrocinador->save($con);
//        }
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $produto1 = new Produto();
//        $produto1->setValorPontos(120);
//        $produto1->save($con);
//
//        $produto2 = new Produto();
//        $produto2->setValorPontos(200);
//        $produto2->save($con);
//
//        $itemPedido1 = new PedidoItem();
//        $itemPedido1->setProdutoVariacao($produto1->getProdutoVariacao());
//        $itemPedido1->setQuantidade(2);
//        $itemPedido1->setPedido($pedido);
//        $itemPedido1->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produto2->getProdutoVariacao());
//        $itemPedido2->setQuantidade(3);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        // 42 (5% de 840 = 120 * 2 + 200 * 3)
//        $arr = array(
//            ClienteQuery::create()->findOneByNome('14.2'),
//            ClienteQuery::create()->findOneByNome('13.2'),
//            ClienteQuery::create()->findOneByNome('12.2'),
//            ClienteQuery::create()->findOneByNome('11.2')
//        );
//        foreach ($arr as $patrocinador) {
//            $this->assertEquals(42, $this->somaPontosCliente($patrocinador, Extrato::TIPO_RESIDUAL, $con));
//        }
//
//        //84 (10% de 840)
//        $arr = array(
//            ClienteQuery::create()->findOneByNome('10.2'),
//            ClienteQuery::create()->findOneByNome('9.2'),
//            ClienteQuery::create()->findOneByNome('8.2')
//        );
//        foreach ($arr as $patrocinador) {
//            $this->assertEquals(84, $this->somaPontosCliente($patrocinador, Extrato::TIPO_RESIDUAL, $con));
//        }
//    }
//
//
//    /**
//     * Testa com uma rede com um nivel menor que 7
//     */
//    public function testBonusResidual3()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $cliente = ClienteQuery::create()->findOneByNome('3.1');
//
//        //cria um plano que concede bonus residual para os patrocinadores
//        $planoResidual = new Plano();
//        $planoResidual->setResidual(true);
//        $planoResidual->save($con);
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoResidual);
//            $patrocinador->save($con);
//        }
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $produto = new Produto();
//        $produto->setValorPontos(100);
//        $produto->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($produto->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        // 5 = 5% de 100 pontos
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('2.1'), Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('1.1'), Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('root'), Extrato::TIPO_RESIDUAL, $con));
//    }
//
//    /**
//     * Testa um caso onde o cliente não foi inserido abaixo do patrocinador solicitado
//     */
//    public function testBonusResidual4()
//    {
//        $gerenciadorPontos = new GerenciadorPontos($con = Propel::getConnection());
//        $gerenciadorRede = new GerenciadorRede($con);
//
//        //cria um plano que concede bonus residual para os patrocinadores
//        $planoResidual = new Plano();
//        $planoResidual->setResidual(true);
//        $planoResidual->save($con);
//
//        $root = new Cliente();
//        $root->setNome('root');
//        $root->setPlano($planoResidual);
//        $gerenciadorRede->insereRede($root);
//
//        $c11 = new Cliente();
//        $c11->setNome('1.1');
//        $c11->setPlano($planoResidual);
//        $gerenciadorRede->insereRede($c11, $root);
//
//        $c12 = new Cliente();
//        $c12->setNome('1.2');
//        $c12->setPlano($planoResidual);
//        $gerenciadorRede->insereRede($c12, $root);
//
//        $c21 = new Cliente();
//        $c21->setNome('2.1');
//        $c21->setPlano($planoResidual);
//        $gerenciadorRede->insereRede($c21, $c11);
//
//        $c22 = new Cliente();
//        $c22->setNome('2.2');
//        $c22->setPlano($planoResidual);
//        $gerenciadorRede->insereRede($c22, $c11);
//
//        $cliente = new Cliente();
//        $cliente->setNome('Cliente 1');
//        $patrocinador = $gerenciadorRede->insereRede($cliente, $c11);
//
//        //Cliente 1 solicitou 1.1 como patrocinador, mas foi inserido abaixo de 2.1 (1.1 ja possui dois filhos)
//        $this->assertEquals('2.1', $patrocinador->getNome());
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $produto = new Produto();
//        $produto->setValorPontos(100);
//        $produto->save($con);
//
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($produto->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $gerenciadorPontos->distribuiPontosPedido($pedido);
//
//        //2.1 não deve receber bonus residual.
//        //Ao contrario das outras distribuicoes, o bonus residual é dado ao patrocinador que foi solicitado (neste caso 1.1) e não o patrocinador onde o cliente foi inserido abaixo (2.1).
//        $this->assertEquals(0, $this->somaPontosCliente($c21, Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente($c11, Extrato::TIPO_RESIDUAL, $con));
//
//        //root também recebe
//        $this->assertEquals(5, $this->somaPontosCliente($root, Extrato::TIPO_RESIDUAL, $con));
//
//        //1.2 e 2.2 não recebem
//        $this->assertEquals(0, $this->somaPontosCliente($c12, Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(0, $this->somaPontosCliente($c22, Extrato::TIPO_RESIDUAL, $con));
//
//        //vamos "forçar" o cliente 1.2 ser o patrocinador indicado de 1.1 e distribuir os pontos novamente.
//        //Agora 1.2 também deve receber.
//        $c11->setClienteRelatedByClienteIndicadorDiretoId($c12);
//        $c11->save($con);
//
//        $this->deleteExtratos(Extrato::TIPO_RESIDUAL, $con);
//
//        $gerenciadorPontos->distribuiPontosPedido($pedido);
//
//        $this->assertEquals(0, $this->somaPontosCliente($c21, Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente($c11, Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente($c12, Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente($root, Extrato::TIPO_RESIDUAL, $con));
//    }
//
//
//    //Verifica se os produtos iniciais de kits de adesão NÃO geram bonus residual
//    public function testBonusResidual5()
//    {
//        $gerenciadorPontos = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        //cria um plano que concede bonus residual para os patrocinadores
//        $planoResidual = new Plano();
//        $planoResidual->setResidual(true);
//        $planoResidual->save($con);
//
//
//        $produto1 = new Produto();
//        $produto1->setValorPontos(100);
//        $produto1->save($con);
//
//        $produtoInicialPlano1 = new Produto();
//        $produtoInicialPlano1->setValorPontos(200);
//        $produtoInicialPlano1->save($con);
//
//        $plano1 = new Plano();
//        $plano1->setGeracaoPontos(200);
//        $plano1->setProdutoId($produtoInicialPlano1->getId());
//        $plano1->save($con);
//
//        $kitAdesaoPlano1 = new Produto();
//        $kitAdesaoPlano1->setValorPontos(200);
//        $kitAdesaoPlano1->setPlanoId($plano1->getId());
//        $kitAdesaoPlano1->save($con);
//
//        $cliente = ClienteQuery::create()->findOneByNome('3.1');
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoResidual);
//            $patrocinador->save($con);
//        }
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        //apenas este item deve ser considerado na distribuicao de pontos.
//        $itemPedido1 = new PedidoItem();
//        $itemPedido1->setProdutoVariacao($produto1->getProdutoVariacao());
//        $itemPedido1->setQuantidade(1);
//        $itemPedido1->setPedido($pedido);
//        $itemPedido1->save($con);
//
//        //este nao deve ser considerado, pois é um kit de adesao
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($kitAdesaoPlano1->getProdutoVariacao());
//        $itemPedido2->setQuantidade(1);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        //este também nao deve ser considerado, pois é um produto inicial de um plano.
//        $itemPedido3 = new PedidoItem();
//        $itemPedido3->setProdutoVariacao($produtoInicialPlano1->getProdutoVariacao());
//        $itemPedido3->setPlanoId($plano1->getId()); //marca que é um item de produto inicial de plano
//        $itemPedido3->setQuantidade(1);
//        $itemPedido3->setPedido($pedido);
//        $itemPedido3->save($con);
//
//        $gerenciadorPontos->distribuiPontosPedido($pedido);
//
//        // 5 = 5% de 100 pontos ($itemPedido1)
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('2.1'), Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('1.1'), Extrato::TIPO_RESIDUAL, $con));
//        $this->assertEquals(5, $this->somaPontosCliente(ClienteQuery::create()->findOneByNome('root'), Extrato::TIPO_RESIDUAL, $con));
//    }
//
//
//    public function testIndicacaoResidualSemBonusNoPlano()
//    {
//        /*
//         * Verifica que os patrocinadores não recebem o bonus de indicacao residual quando seus planos não possuirem este bonus.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $planoSemResidual = new Plano();
//        $planoSemResidual->setResidual(false); //plano sem residual
//        $planoSemResidual->save($con);
//
//        $cliente = ClienteQuery::create()->findOneByNome('15.1');
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            $patrocinador->setPlano($planoSemResidual);
//            $patrocinador->save($con);
//        }
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(90);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(30);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//
//        $produtoNormal = new Produto();
//        $produtoNormal->setValorPontos(120);
//        $produtoNormal->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        /* nao devera ser considerado (kits nao distribuem bonus residual */
//        $itemPedido = new PedidoItem();
//        $itemPedido->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido->setQuantidade(1);
//        $itemPedido->setPedido($pedido);
//        $itemPedido->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoNormal->getProdutoVariacao());
//        $itemPedido2->setQuantidade(1);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        for ($patrocinador = $cliente->getPatrocinador(); $patrocinador !== null; $patrocinador = $patrocinador->getPatrocinador()) {
//            //nenhum patrocinador deve ter recebido o bonus, pois seus planos não permitem
//            $this->assertEquals(0, $this->somaPontosCliente($patrocinador, Extrato::TIPO_RESIDUAL, $con));
//        }
//    }
//
//    /**
//     * Verifica que o patrocinador recebe pontos pelas indicações direta e indireta e também bonus residual
//     */
//    public function testBonusIndicacaoDiretaIndiretaResidual()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        //cria um plano que concede bonus de indicacao direta, indireta e residual para o patrocinador
//        $planoIndicacaoDiretaIndiretaResidual = new Plano();
//        $planoIndicacaoDiretaIndiretaResidual->setIndicacaoDireta(true);
//        $planoIndicacaoDiretaIndiretaResidual->setIndicacaoIndireta(true);
//        $planoIndicacaoDiretaIndiretaResidual->setResidual(true);
//        $planoIndicacaoDiretaIndiretaResidual->save($con);
//
//        $cliente = ClienteQuery::create()->findOneByNome('8.1');
//
//        $cliente->getPatrocinador()->setPlano($planoIndicacaoDiretaIndiretaResidual);
//        $cliente->getPatrocinador()->save($con);
//
//        $pedido = new Pedido();
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        $plano = new Plano();
//        $plano->setGeracaoPontos(120);
//        $plano->save($con);
//
//        $kitAdesao = new Produto();
//        $kitAdesao->setValorPontos(80);
//        $kitAdesao->setPlanoRelatedByPlanoId($plano);
//        $kitAdesao->save($con);
//
//        $itemPedido1 = new PedidoItem();
//        $itemPedido1->setProdutoVariacao($kitAdesao->getProdutoVariacao());
//        $itemPedido1->setQuantidade(1);
//        $itemPedido1->setPedido($pedido);
//        $itemPedido1->save($con);
//
//        $produto = new Produto();
//        $produto->setValorPontos(100);
//        $produto->save($con);
//
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produto->getProdutoVariacao());
//        $itemPedido2->setQuantidade(1);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        // 45 (40 indicacao direta + 0 indicacao indireta + 5 residual)
//        $this->assertEquals(45, $this->somaPontosCliente($cliente->getPatrocinador($con), null, $con));
//    }
//
//    public function testTotalPontosDisponiveisResgate()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente = new Cliente();
//        $cliente->save($con);
//        $this->assertEquals(0.0, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente));
//
//        $cliente2 = new Cliente();
//        $cliente2->save($con);
//        $this->criaExtrato($cliente2, '+', 100.0);
//        $this->criaExtrato($cliente2, '-', 0.75);
//        $this->criaExtrato($cliente2, '+', 403.0);
//        $this->criaExtrato($cliente2, '-', 2);
//        $this->assertEquals(500.25, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente2));
//
//        $cliente3 = new Cliente();
//        $cliente3->save($con);
//        $this->criaExtrato($cliente3, '+', 100.0);
//        $this->criaExtrato($cliente3, '+', 150.0);
//        $this->assertEquals(250, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente3));
//
//        $cliente4 = new Cliente();
//        $cliente4->save($con);
//        $this->criaExtrato($cliente4, '-', 100);
//        $this->criaExtrato($cliente4, '-', 50);
//        $this->criaExtrato($cliente4, '-', 300.25);
//        $this->assertEquals(-450.25, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente4));
//    }
//
//    public function testTotalPontosDisponiveisResgateComPeriodo()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente = new Cliente();
//        $cliente->save($con);
//
//        $this->criaExtrato($cliente, '+', 100.0, new Datetime('2016-11-01'));
//        $this->criaExtrato($cliente, '-', 50.0, new Datetime('2016-11-02'));
//        $this->criaExtrato($cliente, '+', 25.0, new Datetime('2016-11-03'));
//        $this->criaExtrato($cliente, '-', 75.0, new Datetime('2016-11-04'));
//        $this->criaExtrato($cliente, '+', 50.0, new Datetime('2016-11-05'));
//
//        $this->assertEquals(50, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, null, new Datetime('2016-11-02')));
//        $this->assertEquals(-25, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, new Datetime('2016-11-04'), null));
//        $this->assertEquals(-100, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, new DateTime('2016-11-02'), new Datetime('2016-11-04')));
//        $this->assertEquals(50, $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, new Datetime('2016-01-01'), new Datetime('2016-12-31')));
//    }
//
//    public function testResgatePontos()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $this->criaExtrato($cliente1, '+', 75);
//        $this->criaExtrato($cliente1, '+', 25);
//
//        $resgate1 = $this->criaResgate($cliente1, 80);
//
//        $gerenciador->efetuaResgate($resgate1);
//
//        $this->assertEquals(Resgate::SITUACAO_EFETUADO, $resgate1->getSituacao());
//        $this->assertEquals(20, $this->somaPontosCliente($cliente1));
//
//
//        $resgate2 = $this->criaResgate($cliente1, 20);
//
//        $gerenciador->efetuaResgate($resgate2);
//
//        $this->assertEquals(0, $this->somaPontosCliente($cliente1));
//
//        $cliente2 = new Cliente();
//        $cliente2->insertAsFirstChildOf($cliente1);
//        $cliente2->save($con);
//
//        $this->criaExtrato($cliente2, '+', 120.50);
//        $resgate3 = $this->criaResgate($cliente2, 50);
//
//        $gerenciador->efetuaResgate($resgate3);
//
//        $this->assertEquals(70.5, $this->somaPontosCliente($cliente2));
//    }
//
//    /**
//     * Verifica que o gerenciador não permite efetuar o resgate quando o saldo de pontos for insuficiente.
//     *
//     * @expectedException RuntimeException
//     */
//    public function testResgatePontosSemSaldoDisponivel()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $this->criaExtrato($cliente1, '+', 100);
//
//        $resgate1 = $this->criaResgate($cliente1, 101);
//
//        $extratoRet = $gerenciador->tentaEfetuarResgate($resgate1);
//        $this->assertNull($extratoRet); //nao possui saldo suficiente. Deve retornar null.
//
//        $resgate2 = $this->criaResgate($cliente1, 80);
//        $extratoRet = $gerenciador->tentaEfetuarResgate($resgate2);
//        $this->assertNotNull($extratoRet); //este deve passar
//        $this->assertEquals(20.0, $this->somaPontosCliente($cliente1));
//
//        $resgate3 = $this->criaResgate($cliente1, 21.0);
//        $gerenciador->efetuaResgate($resgate3); //o saldo é 20. Deve lançar uma exceção.
//    }
//
//
//    public function testCancelamentoResgatePontos()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $this->criaExtrato($cliente1, '+', 100);
//
//        $resgate1 = $this->criaResgate($cliente1, 50);
//        $extrato1 = $gerenciador->efetuaResgate($resgate1);
//
//        $resgate2 = $this->criaResgate($cliente1, 20);
//        $extrato2 = $gerenciador->efetuaResgate($resgate2);
//        $this->assertEquals(30, $this->somaPontosCliente($cliente1));
//
//        $totalRemovidos = $gerenciador->cancelaResgate($resgate2);
//        $this->assertEquals(1, $totalRemovidos);
//        $this->assertEquals(Resgate::SITUACAO_NAOEFETUADO, $resgate2->getSituacao());
//        $this->assertEquals(50, $this->somaPontosCliente($cliente1));
//        $this->assertEquals(0, ExtratoQuery::create()->filterById($extrato2->getId())->count($con)); //extrato 2 deve ter sido removido.
//    }
//
//    public function testMarcarResgatePontosComoPendente()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $this->criaExtrato($cliente1, '+', 200);
//
//        $resgate1 = $this->criaResgate($cliente1, 120);
//        $extrato1 = $gerenciador->efetuaResgate($resgate1);
//
//        $resgate2 = $this->criaResgate($cliente1, 70);
//        $extrato2 = $gerenciador->efetuaResgate($resgate2);
//        $this->assertEquals(10, $this->somaPontosCliente($cliente1));
//
//        $totalRemovidos = $gerenciador->marcaResgateComoPendente($resgate2);
//        $this->assertEquals(1, $totalRemovidos);
//        $this->assertEquals(Resgate::SITUACAO_PENDENTE, $resgate2->getSituacao());
//        $this->assertEquals(80, $this->somaPontosCliente($cliente1));
//        $this->assertEquals(0, ExtratoQuery::create()->filterById($extrato2->getId())->count($con)); //extrato 2 deve ter sido removido.
//
//
//        $cliente2 = new Cliente();
//        $cliente2->insertAsFirstChildOf($cliente1);
//        $cliente2->save($con);
//
//        $this->criaExtrato($cliente2, '+', 500);
//
//        $resgate3 = $this->criaResgate($cliente2, 100);
//
//        $gerenciador->cancelaResgate($resgate3);
//
//        $totalRemovidos = $gerenciador->marcaResgateComoPendente($resgate3);
//        $this->assertEquals(0, $totalRemovidos); //O resgate estava como não efetuado. Nenhum extrato deve ter ser removido.
//        $this->assertEquals(Resgate::SITUACAO_PENDENTE, $resgate3->getSituacao());
//        $this->assertEquals(500, $this->somaPontosCliente($cliente2));
//    }
//
//
//    public function testExpiracaoPontos()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        //Observação: para facilitar, cria os extratos com a mesma data de criação e expiração.
//        $this->criaExtrato($cliente1, '+', 100, ($dt = new DateTime('2016-11-24')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente1, '+', 50, ($dt = new DateTime('2016-11-24')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente1, '+', 100, ($dt = new DateTime('2016-11-25')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente1, '+', 50, ($dt = new DateTime('2016-11-26')))->setDataExpiracao($dt);
//
//        $extrato = $this->criaExtrato($cliente1, '-', 25);
//
//        $expirados = $gerenciador->expiraPontosCliente($cliente1, new Datetime('2016-11-23'));
//        //nenhum estrato negativo deve ter sido criados
//        $this->assertEquals(0, count($expirados));
//        $this->assertEquals(275, $this->somaPontosCliente($cliente1));
//
//        $expirados = $gerenciador->expiraPontosCliente($cliente1, new DateTime('2016-11-25'));
//        //deve ter ter expirado os extratos do dia 24 (125 = 150 originais - 25 ja resgatados)
//        $this->assertEquals(1, count($expirados));
//        $this->assertEquals(125, $expirados[0]->getPontos());
//        $this->assertEquals('-', $expirados[0]->getOperacao());
//        $this->assertEquals(150, $this->somaPontosCliente($cliente1));
//
//        $cliente2 = new Cliente();
//        $cliente2->insertAsFirstChildOf($cliente1);
//        $cliente2->save($con);
//
//        $this->criaExtrato($cliente2, '+', 25, ($dt = new DateTime('2016-12-01')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente2, '+', 50, ($dt = new DateTime('2016-12-01')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente2, '+', 40, ($dt = new DateTime('2016-12-02')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente2, '+', 35, ($dt = new DateTime('2016-12-02')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente2, '+', 30, ($dt = new DateTime('2016-12-03')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente2, '+', 20, ($dt = new DateTime('2016-12-03')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente2, '-', 60);
//
//        $expirados = $gerenciador->expiraPontosCliente($cliente2, new Datetime('2016-12-03'));
//        $this->assertEquals(2, count($expirados)); //deve ter expirado os extratos do dia 01 e 02.
//        $this->assertEquals(15, $expirados[0]->getPontos()); // expirou 15 do 01 (75 - 60 usados)
//        $this->assertEquals('-', $expirados[0]->getOperacao());
//        $this->assertEquals(75, $expirados[1]->getPontos()); //expirou os 75 pontos do dia 02
//        $this->assertEquals('-', $expirados[1]->getOperacao());
//
//        $this->assertEquals(50, $this->somaPontosCliente($cliente2));
//
//        $cliente3 = new Cliente();
//        $cliente3->insertAsLastChildOf($cliente1);
//        $cliente3->save($con);
//
//        $this->criaExtrato($cliente3, '+', 25.20, ($dt = new DateTime('2016-12-11')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente3, '+', 50.15, ($dt = new DateTime('2016-12-11')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente3, '+', 40.77, ($dt = new DateTime('2016-12-12')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente3, '+', 35.18, ($dt = new DateTime('2016-12-12')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente3, '+', 30.25, ($dt = new DateTime('2016-12-13')))->setDataExpiracao($dt);
//        $this->criaExtrato($cliente3, '+', 20.75, ($dt = new DateTime('2016-12-13')))->setDataExpiracao($dt);
//
//        $this->criaExtrato($cliente3, '-', 30.45);
//        $this->criaExtrato($cliente3, '-', 50.00);
//        $this->criaExtrato($cliente3, '-', 40.52);
//
//        $expirados = $gerenciador->expiraPontosCliente($cliente3, new Datetime('2016-12-12 17:00'));
//        $this->assertEquals(1, count($expirados));
//        $this->assertEquals(30.33, $expirados[0]->getPontos());
//        $this->assertEquals('-', $expirados[0]->getOperacao());
//
//        $this->assertEquals(51, $this->somaPontosCliente($cliente3));
//    }
//
//
//    public function testCriacaoExtratoPagamentoPedido()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $cliente1 = new Cliente();
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $this->criaExtrato($cliente1, '+', 200);
//
//        $pedido1 = new Pedido();
//        $pedido1->setCliente($cliente1);
//        $pedido1->save($con);
//
//        $extrato = $gerenciador->criaExtratoParaPagamentoDePedido($pedido1, 50);
//        $this->assertEquals(Extrato::TIPO_PAGAMENTO_PEDIDO, $extrato->getTipo());
//        $this->assertEquals(50.0, $extrato->getPontos());
//        $this->assertEquals(150, $this->somaPontosCliente($cliente1));
//
//        $pedido2 = new Pedido();
//        $pedido2->setCliente($cliente1);
//        $pedido2->save($con);
//
//        //testa pagamento parcial
//        $extrato = $gerenciador->criaExtratoParaPagamentoDePedido($pedido2, 30, true);
//        $this->assertEquals(Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO, $extrato->getTipo());
//        $this->assertEquals(30.00, $extrato->getPontos());
//        $this->assertEquals(120, $this->somaPontosCliente($cliente1));
//    }
//
//
//    public function testVendaFranqueado()
//    {
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $plano = new Plano();
//        $plano->setDescontoRevenda(20);
//        $plano->save($con);
//
//        $produto1 = new Produto();
//        $produto1->save();
//
//        $cliente1 = new Cliente();
//        $cliente1->setNome('Cliente 1');
//        $cliente1->setPlano($plano);
//        $cliente1->makeRoot();
//        $cliente1->save($con);
//
//        $hotsite1 = new Hotsite();
//        $hotsite1->setCliente($cliente1);
//        $hotsite1->save($con);
//
//        $cliente2 = new Cliente();
//        $cliente2->setNome('Cliente 2');
//        $cliente2->save($con);
//
//        //cria um pedido tendo cliente 1 como revenda (via hotsite 1)
//        $pedido1 = new Pedido();
//        $pedido1->setHotsite($hotsite1);
//        $pedido1->setCliente($cliente2);
//        $pedido1->setValorEntrega(25);
//        $pedido1->save($con);
//
//        $item1 = new PedidoItem();
//        $item1->setProdutoVariacao($produto1->getProdutoVariacao());
//        $item1->setQuantidade(2);
//        $item1->setValorUnitario(75);
//        $item1->setPedido($pedido1);
//        $item1->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido1);
//
//        //deve criar um extrato com 30 pontos (20% de 150 - nao deve considerar o frete) para o cliente 1
//        $this->assertEquals(30, $this->somaPontosCliente($cliente1, Extrato::TIPO_VENDA_FRANQUEADO));
//
//        $gerenciador->distribuiPontosPedido($pedido1);
//
//        //garente que nao distribui mais de uma vez
//        $this->assertEquals(30, $this->somaPontosCliente($cliente1, Extrato::TIPO_VENDA_FRANQUEADO));
//    }
//
//    public function testHotfixTicket3121()
//    {
//        /*
//         * Testa a situação informada no ticket 3121.
//         * No caso, produtos iniciais de planos não devem gerar pontos de venda de franqueado.
//         */
//
//        $gerenciador = new GerenciadorPontos($con = Propel::getConnection());
//
//        $this->criarRedeTeste();
//
//        $planoFranqueado = new Plano();
//        $planoFranqueado->setDescontoRevenda(20);
//        $planoFranqueado->save($con);
//
//        $produto1 = new Produto();
//        $produto1->setValorPontos(100);
//        $produto1->save($con);
//
//        $produto2 = new Produto();
//        $produto2->setValorPontos(25);
//        $produto2->save($con);
//
//        $produtoInicialPlano1 = new Produto();
//        $produtoInicialPlano1->setValorPontos(200);
//        $produtoInicialPlano1->save($con);
//
//        $plano1 = new Plano();
//        $plano1->setGeracaoPontos(200);
//        $plano1->setProdutoId($produtoInicialPlano1->getId());
//        $plano1->save($con);
//
//        $kitAdesaoPlano1 = new Produto();
//        $kitAdesaoPlano1->setValorPontos(200);
//        $kitAdesaoPlano1->setPlanoId($plano1->getId());
//        $kitAdesaoPlano1->save($con);
//
//        //Cliente do hotsite por onde foram feitas as vendas
//        $clienteHotsite = ClienteQuery::create()->findOneByNome('1.1');
//        $clienteHotsite->setPlano($planoFranqueado);
//        $clienteHotsite->save($con);
//
//        $hotsite = new Hotsite();
//        $hotsite->setCliente($clienteHotsite);
//        $hotsite->save($con);
//
//        //cliente do pedido (nao precisa estar na rede)
//        $cliente = new Cliente();
//        $cliente->setNome('Cliente 1');
//        $cliente->save($con);
//
//        //Faz um pedido contratando um plano
//        $pedido = new Pedido();
//        $pedido->setHotsite($hotsite); /* indica que o pedido foi feito por um hotsite */
//        $pedido->setCliente($cliente);
//        $pedido->save($con);
//
//        //item do kit de adesao
//        $itemPedido1 = new PedidoItem();
//        $itemPedido1->setProdutoVariacao($kitAdesaoPlano1->getProdutoVariacao());
//        $itemPedido1->setQuantidade(1);
//        $itemPedido1->setValorUnitario(100);
//        $itemPedido1->setPedido($pedido);
//        $itemPedido1->save($con);
//
//        //item do produto inicial
//        $itemPedido2 = new PedidoItem();
//        $itemPedido2->setProdutoVariacao($produtoInicialPlano1->getProdutoVariacao());
//        $itemPedido2->setPlanoId($plano1->getId()); //marca que é um item de produto inicial de plano
//        $itemPedido2->setQuantidade(1);
//        $itemPedido2->setValorUnitario(100);
//        $itemPedido2->setPedido($pedido);
//        $itemPedido2->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido);
//
//        //Aqui estava o erro. Não deve gerar pontos para o franqueado (apenas pedidos de produtos normais)
//        $this->assertEquals(0, $this->somaPontosCliente($clienteHotsite, Extrato::TIPO_VENDA_FRANQUEADO));
//
//        //Agora faz um pedido com um produto normal. Neste caso deve gerar pontos para o franquado.
//
//        $pedido2 = new Pedido();
//        $pedido2->setHotsite($hotsite); /* indica que o pedido foi feito por um hotsite */
//        $pedido2->setCliente($cliente);
//        $pedido2->save($con);
//
//        $itemPedido3 = new PedidoItem();
//        $itemPedido3->setProdutoVariacao($produto1->getProdutoVariacao());
//        $itemPedido3->setQuantidade(1);
//        $itemPedido3->setValorUnitario(100);
//        $itemPedido3->setPedido($pedido2);
//        $itemPedido3->save($con);
//
//        $itemPedido4 = new PedidoItem();
//        $itemPedido4->setProdutoVariacao($produto2->getProdutoVariacao());
//        $itemPedido4->setQuantidade(2);
//        $itemPedido4->setValorUnitario(25);
//        $itemPedido4->setPedido($pedido2);
//        $itemPedido4->save($con);
//
//        $gerenciador->distribuiPontosPedido($pedido2);
//
//        //deste pedido sim, o franqueado deve receber pontos pois tem um produto normal
//        //30 = 20% do valor dos itens do pedido2 (150,00)
//        $this->assertEquals(30, $this->somaPontosCliente($clienteHotsite, Extrato::TIPO_VENDA_FRANQUEADO));
//    }
//
//
//    /**
//     *
//     * @param Cliente $cliente
//     * @param string $operacao
//     * @param float $pontos
//     * @param DateTime $data
//     * @return \Extrato
//     */
//    protected function criaExtrato(Cliente $cliente, $operacao, $pontos, DateTime $data = null)
//    {
//        if (null === $data) {
//            $data = new Datetime();
//        }
//
//        $extrato = new Extrato();
//        $extrato->setCliente($cliente);
//        $extrato->setOperacao($operacao);
//        $extrato->setPontos($pontos);
//        $extrato->setData($data);
//        $extrato->save();
//
//        return $extrato;
//    }
//
//    /**
//     *
//     * @param Cliente $cliente
//     * @param int $valor
//     * @return \Resgate
//     */
//    protected function criaResgate(Cliente $cliente, $valor)
//    {
//        $resgate = new Resgate();
//        $resgate->setCliente($cliente);
//        $resgate->setValor($valor);
//        $resgate->setSituacao(Resgate::SITUACAO_PENDENTE);
//        return $resgate;
//    }
//
//    /**
//     *
//     * @param float $pontos
//     * @param int $decimals
//     * @return string
//     */
//    protected function formataPontos($pontos, $decimals = 2)
//    {
//        return number_format($pontos, $decimals, '.', '');
//    }
//
//

    /**
     * @param Cliente $cliente
     * @param null $tipoExtrato
     * @param PropelPDO|null $con
     * @return float
     * @throws \PropelException
     */
    private function somaPontosCliente(Cliente $cliente, $tipoExtrato = null, PropelPDO $con = null)
    {
        $query = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByBloqueado(false);

        if ($tipoExtrato !== null) {
            $query->filterByTipo($tipoExtrato);
        }

        $soma = 0.0;

        foreach ($query->find($con) as $extrato) :
            /* @var $extrato Extrato */
            if ('+' === $extrato->getOperacao()) :
                $soma += $extrato->getPontos();
            else :
                $soma -= $extrato->getPontos();
            endif;
        endforeach;

        return $soma;
    }
//
//    /**
//     *
//     * @param Cliente $cliente
//     * @param Pedido $pedido
//     * @param string $tipoExtrato
//     * @param PropelPDO $con
//     * @return float
//     */
//    protected function somaPontosPatrocinadorNoPedido(Cliente $cliente, Pedido $pedido, $tipoExtrato = null, PropelPDO $con = null)
//    {
//        $query = ExtratoQuery::create()
//                        ->filterByCliente($cliente)
//                        ->filterByPedido($pedido)
//                        ->filterByBloqueado(false);
//
//        if ($tipoExtrato !== null) {
//            $query->filterByTipo($tipoExtrato);
//        }
//
//        $soma = 0.0;
//        foreach ($query->find($con) as $extrato) { /* @var $extrato Extrato */
//            if ('+' === $extrato->getOperacao()) {
//                $soma += $extrato->getPontos();
//            } else {
//                $soma -= $extrato->getPontos();
//            }
//        }
//        return $soma;
//    }
//
//    /**
//     *
//     * @param string $tipoExtrato
//     * @param PropelPDO $con
//     * @return int
//     */
//    protected function deleteExtratos($tipoExtrato, PropelPDO $con = null)
//    {
//        $query = ExtratoQuery::create()
//                        ->filterByTipo($tipoExtrato);
//
//        return $query->delete($con);
//    }
//
//    /**
//     * Criar uma rede de clientes com mais de 10 niveis para usar nos testes.
//     * Estrutura da rede:
//     *              root
//     *      1.1             1.2
//     *      2.1             2.2
//     *      3.1             3.2
//     *      4.1             4.2
//     *      5.1             5.2
//     *      6.1             6.2
//     *      7.1             7.2
//     *      8.1             8.2
//     *      9.1             9.2
//     *      10.1            10.2
//     *      11.1            11.2
//     *      12.1            12.2
//     *      13.1            13.2
//     *      14.1            14.2
//     *      15.1            15.2
//     */
//    protected function criarRedeTeste()
//    {
//        $con = Propel::getConnection();
//
//        //root
//        $root = new Cliente();
//        $root->setNome('root');
//        $root->makeRoot();
//        $root->save($con);
//
//        //1.1
//        $c11 = new Cliente();
//        $c11->setNome('1.1');
//        $c11->insertAsFirstChildOf($root);
//        $c11->setClienteRelatedByClienteIndicadorDiretoId($root);
//        $c11->save($con);
//
//        //2.1
//        $c21 = new Cliente();
//        $c21->setNome('2.1');
//        $c21->insertAsFirstChildOf($c11);
//        $c21->setClienteRelatedByClienteIndicadorDiretoId($c11);
//        $c21->save($con);
//
//        //3.1
//        $c31 = new Cliente();
//        $c31->setNome('3.1');
//        $c31->insertAsFirstChildOf($c21);
//        $c31->setClienteRelatedByClienteIndicadorDiretoId($c21);
//        $c31->save($con);
//
//        //4.1
//        $c41 = new Cliente();
//        $c41->setNome('4.1');
//        $c41->insertAsFirstChildOf($c31);
//        $c41->setClienteRelatedByClienteIndicadorDiretoId($c31);
//        $c41->save($con);
//
//        //5.1
//        $c51 = new Cliente();
//        $c51->setNome('5.1');
//        $c51->insertAsFirstChildOf($c41);
//        $c51->setClienteRelatedByClienteIndicadorDiretoId($c41);
//        $c51->save($con);
//
//        //6.1
//        $c61 = new Cliente();
//        $c61->setNome('6.1');
//        $c61->insertAsFirstChildOf($c51);
//        $c61->setClienteRelatedByClienteIndicadorDiretoId($c51);
//        $c61->save($con);
//
//        //7.1
//        $c71 = new Cliente();
//        $c71->setNome('7.1');
//        $c71->insertAsFirstChildOf($c61);
//        $c71->setClienteRelatedByClienteIndicadorDiretoId($c61);
//        $c71->save($con);
//
//        //8.1
//        $c81 = new Cliente();
//        $c81->setNome('8.1');
//        $c81->insertAsFirstChildOf($c71);
//        $c81->setClienteRelatedByClienteIndicadorDiretoId($c71);
//        $c81->save($con);
//
//        //9.1
//        $c91 = new Cliente();
//        $c91->setNome('9.1');
//        $c91->insertAsFirstChildOf($c81);
//        $c91->setClienteRelatedByClienteIndicadorDiretoId($c81);
//        $c91->save($con);
//
//        //10.1
//        $c101 = new Cliente();
//        $c101->setNome('10.1');
//        $c101->insertAsFirstChildOf($c91);
//        $c101->setClienteRelatedByClienteIndicadorDiretoId($c91);
//        $c101->save($con);
//
//        //11.1
//        $c111 = new Cliente();
//        $c111->setNome('11.1');
//        $c111->insertAsFirstChildOf($c101);
//        $c111->setClienteRelatedByClienteIndicadorDiretoId($c101);
//        $c111->save($con);
//
//        //12.1
//        $c121 = new Cliente();
//        $c121->setNome('12.1');
//        $c121->insertAsFirstChildOf($c111);
//        $c121->setClienteRelatedByClienteIndicadorDiretoId($c111);
//        $c121->save($con);
//
//        //13.1
//        $c131 = new Cliente();
//        $c131->setNome('13.1');
//        $c131->insertAsFirstChildOf($c121);
//        $c131->setClienteRelatedByClienteIndicadorDiretoId($c121);
//        $c131->save($con);
//
//        //14.1
//        $c141 = new Cliente();
//        $c141->setNome('14.1');
//        $c141->insertAsFirstChildOf($c131);
//        $c141->setClienteRelatedByClienteIndicadorDiretoId($c131);
//        $c141->save($con);
//
//        //15.1
//        $c151 = new Cliente();
//        $c151->setNome('15.1');
//        $c151->insertAsFirstChildOf($c141);
//        $c151->setClienteRelatedByClienteIndicadorDiretoId($c141);
//        $c151->save($con);
//
//        //1.2
//        $c12 = new Cliente();
//        $c12->setNome('1.2');
//        $c12->insertAsLastChildOf($root);
//        $c12->setClienteRelatedByClienteIndicadorDiretoId($root);
//        $c12->save($con);
//
//        //2.2
//        $c22 = new Cliente();
//        $c22->setNome('2.2');
//        $c22->insertAsFirstChildOf($c12);
//        $c22->setClienteRelatedByClienteIndicadorDiretoId($c12);
//        $c22->save($con);
//
//        //3.2
//        $c32 = new Cliente();
//        $c32->setNome('3.2');
//        $c32->insertAsFirstChildOf($c22);
//        $c32->setClienteRelatedByClienteIndicadorDiretoId($c22);
//        $c32->save($con);
//
//        //4.2
//        $c42 = new Cliente();
//        $c42->setNome('4.2');
//        $c42->insertAsFirstChildOf($c32);
//        $c42->setClienteRelatedByClienteIndicadorDiretoId($c32);
//        $c42->save($con);
//
//        //5.2
//        $c52 = new Cliente();
//        $c52->setNome('5.2');
//        $c52->insertAsFirstChildOf($c42);
//        $c52->setClienteRelatedByClienteIndicadorDiretoId($c42);
//        $c52->save($con);
//
//        //6.2
//        $c62 = new Cliente();
//        $c62->setNome('6.2');
//        $c62->insertAsFirstChildOf($c52);
//        $c62->setClienteRelatedByClienteIndicadorDiretoId($c52);
//        $c62->save($con);
//
//        //7.2
//        $c72 = new Cliente();
//        $c72->setNome('7.2');
//        $c72->insertAsFirstChildOf($c62);
//        $c72->setClienteRelatedByClienteIndicadorDiretoId($c62);
//        $c72->save($con);
//
//        //8.2
//        $c82 = new Cliente();
//        $c82->setNome('8.2');
//        $c82->insertAsFirstChildOf($c72);
//        $c82->setClienteRelatedByClienteIndicadorDiretoId($c72);
//        $c82->save($con);
//
//        //9.2
//        $c92 = new Cliente();
//        $c92->setNome('9.2');
//        $c92->insertAsFirstChildOf($c82);
//        $c92->setClienteRelatedByClienteIndicadorDiretoId($c82);
//        $c92->save($con);
//
//        //10.2
//        $c102 = new Cliente();
//        $c102->setNome('10.2');
//        $c102->insertAsFirstChildOf($c92);
//        $c102->setClienteRelatedByClienteIndicadorDiretoId($c92);
//        $c102->save($con);
//
//        //11.2
//        $c112 = new Cliente();
//        $c112->setNome('11.2');
//        $c112->insertAsFirstChildOf($c102);
//        $c112->setClienteRelatedByClienteIndicadorDiretoId($c102);
//        $c112->save($con);
//
//        //12.2
//        $c122 = new Cliente();
//        $c122->setNome('12.2');
//        $c122->insertAsFirstChildOf($c112);
//        $c122->setClienteRelatedByClienteIndicadorDiretoId($c112);
//        $c122->save($con);
//
//        //13.2
//        $c132 = new Cliente();
//        $c132->setNome('13.2');
//        $c132->insertAsFirstChildOf($c122);
//        $c132->setClienteRelatedByClienteIndicadorDiretoId($c122);
//        $c132->save($con);
//
//        //14.2
//        $c142 = new Cliente();
//        $c142->setNome('14.2');
//        $c142->insertAsFirstChildOf($c132);
//        $c142->setClienteRelatedByClienteIndicadorDiretoId($c132);
//        $c142->save($con);
//
//        //15.2
//        $c152 = new Cliente();
//        $c152->setNome('15.2');
//        $c152->insertAsFirstChildOf($c142);
//        $c152->setClienteRelatedByClienteIndicadorDiretoId($c142);
//        $c152->save($con);
//    }
}

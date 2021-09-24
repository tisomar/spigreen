<?php

require_once './MyDatabaseTestCase.php';

/**
 * Description of GerenciadorPlanoCarreiraTest
 *
 * @author André Garlini
 */
class GerenciadorPlanoCarreiraTest extends MyDatabaseTestCase
{
    
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/myXmlFixture.xml');
    }
    
    
    public function testTotalPontosCliente()
    {
        $gerenciador = new GerenciadorPlanoCarreira($con = Propel::getConnection());
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->save($con);
        
        $this->criaExtrato($cliente1, '+', 100.0);
        $this->criaExtrato($cliente1, '+', 20.0);
        $this->criaExtrato($cliente1, '+', 30.0)->setBloqueado(true)->save();
                
        $this->assertEquals(120.0, $gerenciador->getTotalPontosCliente($cliente1));
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->save($con);
        
        $this->criaExtrato($cliente2, '+', 50.25);
        $this->criaExtrato($cliente2, '+', 20.25);
        $this->criaExtrato($cliente2, '+', 10, null, Extrato::TIPO_INDICACAO_DIRETA);
        
        $this->assertEquals(70.50, $gerenciador->getTotalPontosCliente($cliente2));
        
        $cliente3 = new Cliente();
        $cliente3->setNome('Cliente 3');
        $cliente3->save($con);
        
        $this->criaExtrato($cliente3, '+', 5000);
        //testa passando o parametro $nivel
        $this->assertEquals(5000, $gerenciador->getTotalPontosCliente($cliente3, $nivel));
        $this->assertEquals(2, $nivel);
    }
    
    
    public function testeNivelClientesPelosPontos()
    {
        $gerenciador = new GerenciadorPlanoCarreira($con = Propel::getConnection());
        
        $this->assertEquals(0, $gerenciador->getNivelClientePelosPontos(0));
        $this->assertEquals(0, $gerenciador->getNivelClientePelosPontos(999));
        
        $this->assertEquals(1, $gerenciador->getNivelClientePelosPontos(1000));
        $this->assertEquals(1, $gerenciador->getNivelClientePelosPontos(4999));
        
        $this->assertEquals(2, $gerenciador->getNivelClientePelosPontos(5000));
        $this->assertEquals(2, $gerenciador->getNivelClientePelosPontos(9999));
        
        $this->assertEquals(3, $gerenciador->getNivelClientePelosPontos(10000));
        $this->assertEquals(3, $gerenciador->getNivelClientePelosPontos(34999));
        
        $this->assertEquals(4, $gerenciador->getNivelClientePelosPontos(35000));
        $this->assertEquals(4, $gerenciador->getNivelClientePelosPontos(79999));
        
        $this->assertEquals(5, $gerenciador->getNivelClientePelosPontos(80000));
        $this->assertEquals(5, $gerenciador->getNivelClientePelosPontos(199999));
        
        $this->assertEquals(6, $gerenciador->getNivelClientePelosPontos(200000));
        $this->assertEquals(6, $gerenciador->getNivelClientePelosPontos(399999));
        
        $this->assertEquals(7, $gerenciador->getNivelClientePelosPontos(400000));
        $this->assertEquals(7, $gerenciador->getNivelClientePelosPontos(799999));
        
        $this->assertEquals(8, $gerenciador->getNivelClientePelosPontos(800000));
        $this->assertEquals(8, $gerenciador->getNivelClientePelosPontos(1399999));
        
        $this->assertEquals(9, $gerenciador->getNivelClientePelosPontos(1400000));
        $this->assertEquals(9, $gerenciador->getNivelClientePelosPontos(2999999));
        
        $this->assertEquals(10, $gerenciador->getNivelClientePelosPontos(3000000));
        $this->assertEquals(10, $gerenciador->getNivelClientePelosPontos(3000001));
        $this->assertEquals(10, $gerenciador->getNivelClientePelosPontos(10000000));
    }
    
    
    public function testDistribuicaoClientes()
    {
        $gerenciador = new GerenciadorPlanoCarreira($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setPlanoCarreira(true);
        $plano->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->setPlano($plano);
        $cliente1->save($con);
        
        $this->criaExtrato($cliente1, '+', 100.0);
        $this->criaExtrato($cliente1, '+', 20.0);
        $this->criaExtrato($cliente1, '+', 30.0)->setBloqueado(true)->save();
                
        $this->assertEquals(120.0, $gerenciador->getTotalPontosCliente($cliente1));
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->setPlano($plano);
        $cliente2->save($con);
        
        $this->criaExtrato($cliente2, '+', 50.25);
        $this->criaExtrato($cliente2, '+', 20.25);
        $this->criaExtrato($cliente2, '+', 10000);
        
        $this->assertEquals(10070.50, $gerenciador->getTotalPontosCliente($cliente2, $nivel));
        $this->assertEquals(3, $nivel);
        
        $cliente3 = new Cliente();
        $cliente3->setNome('Cliente 3');
        $cliente3->setPlano($plano);
        $cliente3->save($con);
        
        $this->criaExtrato($cliente3, '+', 5000);
        $this->criaExtrato($cliente3, '+', 30000);
        $this->assertEquals(35000, $gerenciador->getTotalPontosCliente($cliente3, $nivel));
        $this->assertEquals(4, $nivel);
        
        $gerenciador->distribuiBonusClientes();
        
        /* nao possui pontos suficientes */
        $this->assertNull($this->findUltimaDistribuicaoCliente($cliente1));
        
        /* deve receber 800 pontos, nivel 3 */
        $this->assertEquals(800, $this->findUltimaDistribuicaoCliente($cliente2)->getPontos());
        //tambem deve ter recebido um extrato
        $this->assertEquals(800, $this->findUltimoExtratoCliente($cliente2)->getPontos());
        
        /* deve receber 2800 pontos, nivel 4 */
        $this->assertEquals(2800, $this->findUltimaDistribuicaoCliente($cliente3)->getPontos());
        $this->assertEquals(2800, $this->findUltimoExtratoCliente($cliente3)->getPontos());
    }
    
    public function testDistribuicaoMaisDeUmNivel()
    {
        /* Testa um caso que o cliente sobe mais de 1 nivel de uma vez só */
        
        $gerenciador = new GerenciadorPlanoCarreira($con = Propel::getConnection());
        
        $plano = new Plano();
        $plano->setPlanoCarreira(true);
        $plano->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setPlano($plano);
        $cliente1->save($con);
        
        $this->criaExtrato($cliente1, '+', 1000);
        
        $this->assertEquals(1000, $gerenciador->getTotalPontosCliente($cliente1, $nivel));
        $this->assertEquals(1, $nivel);
        
        $gerenciador->distribuiBonusClientes();
        
        $this->assertEquals(80, $this->findUltimaDistribuicaoCliente($cliente1)->getPontos());
        
        $this->criaExtrato($cliente1, '+', 4000);
        
        $this->assertEquals(5000, $gerenciador->getTotalPontosCliente($cliente1, $nivel));
        $this->assertEquals(2, $nivel);
        
        $this->criaExtrato($cliente1, '+', 5000);
        
        $this->assertEquals(10e3, $gerenciador->getTotalPontosCliente($cliente1, $nivel));
        $this->assertEquals(3, $nivel);
        
        $gerenciador->distribuiBonusClientes();
        
        $distribuicoesCliente = PlanoCarreiraClienteQuery::create()
                                    ->filterByCliente($cliente1)
                                    ->orderById()
                                    ->find();
        
        $this->assertEquals(3, count($distribuicoesCliente));
        
        $this->assertEquals(80, $distribuicoesCliente[0]->getPontos()); /* bonus nivel 1 */
        
        /* deve ter sido gerado uma distribuicacao tanto para o nivel 2 quanto para o nivel 3 */
        $this->assertEquals(400, $distribuicoesCliente[1]->getPontos());
        $this->assertEquals(800, $distribuicoesCliente[2]->getPontos());
    }
    
    public function testDistribuicaoSemBonusNoPlano()
    {
        /* verifica que o cliente não recebe o bonus quando seu plano nao concede bonus de plano de carreita */
        
        $gerenciador = new GerenciadorPlanoCarreira($con = Propel::getConnection());
        
        $planoSemBonus = new Plano();
        $planoSemBonus->setPlanoCarreira(false);
        $planoSemBonus->save($con);
        
        $planoComBonus = new Plano();
        $planoComBonus->setPlanoCarreira(true);
        $planoComBonus->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->setPlano($planoSemBonus);
        $cliente1->save($con);
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->setPlano($planoComBonus);
        $cliente2->save($con);
        
        $this->criaExtrato($cliente1, '+', 200000);
        $this->assertEquals(200e3, $gerenciador->getTotalPontosCliente($cliente1));
        
        $this->criaExtrato($cliente2, '+', 500000);
        $this->assertEquals(500e3, $gerenciador->getTotalPontosCliente($cliente2));
        
        $gerenciador->distribuiBonusClientes();
        
        /* plano nao concede bonus */
        $this->assertNull($this->findUltimaDistribuicaoCliente($cliente1));
        
        $this->assertEquals(32e3, $this->findUltimaDistribuicaoCliente($cliente2)->getPontos());
    }
        
    protected function criaExtrato(Cliente $cliente, $operacao, $pontos, DateTime $data = null, $tipo = Extrato::TIPO_REDE_BINARIA)
    {
        if (null === $data) {
            $data = new Datetime();
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
     * @return PlanoCarreiraCliente|null
     */
    protected function findUltimaDistribuicaoCliente(Cliente $cliente)
    {
        return PlanoCarreiraClienteQuery::create()
                    ->filterByCliente($cliente)
                    ->orderById(Criteria::DESC)
                    ->findOne();
    }
    
    /**
     *
     * @param Cliente $cliente
     * @param string|null $tipo
     * @return Extrato|null
     */
    protected function findUltimoExtratoCliente(Cliente $cliente, $tipo = Extrato::TIPO_PLANO_CARREIRA)
    {
        return ExtratoQuery::create()
                    ->filterByCliente($cliente)
                    ->filterByTipo($tipo)
                    ->orderById(Criteria::DESC)
                    ->findOne();
    }
}

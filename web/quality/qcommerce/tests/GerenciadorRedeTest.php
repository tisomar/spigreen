<?php

require_once './MyDatabaseTestCase.php';

/**
 * Description of GerenciadorRedeTest
 *
 * @author André Garlini
 */
class GerenciadorRedeTest extends MyDatabaseTestCase
{
    
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/myXmlFixture.xml');
    }
    
    
    public function testInsercaoRoot()
    {
        $gerenciador = new GerenciadorRede(Propel::getConnection());
        
        $root = new Cliente();
        $root->gerarChaveIndicacao();
        $root->save();
        
        $gerenciador->insereRoot($root);
        
        $this->assertNotNull(ClienteQuery::create()->findRoot());
        
        $this->assertEquals($root->getId(), ClienteQuery::create()->findRoot()->getId());
    }
    
    /**
     * Verifica se uma exceção é lançada ao tentar inserir um segundo cliente como root.
     *
     * @expectedException LogicException
     */
    public function testInsercaoRootException()
    {
        $gerenciador = new GerenciadorRede(Propel::getConnection());
        
        $root = new Cliente();
        $root->gerarChaveIndicacao();
        $root->save();
        
        $gerenciador->insereRoot($root);
        
        $cliente2 = new Cliente();
        $cliente2->gerarChaveIndicacao();
        $cliente2->save();
        $gerenciador->insereRoot($cliente2);
    }
    
    /**
     * Verifica se uma exceção é lançada ao tentar reinserir um cliente na rede.
     *
     * @expectedException LogicException
     */
    public function testReinsercaoException()
    {
        $gerenciador = new GerenciadorRede(Propel::getConnection());
        
        $root = new Cliente();
        $gerenciador->insereRoot($root);
        
        $cliente1 = new Cliente();
        $gerenciador->insereRede($cliente1, $root);
        
        $cliente2 = new Cliente();
        $gerenciador->insereRede($cliente2, $root);
        
        $gerenciador->insereRede($cliente1, $cliente2);
    }
    
    
    /**
     * Verifica se uma exceção é lançada ao tentar inserir um cliente com um patrocinador fora da rede.
     *
     * @expectedException LogicException
     */
    public function testInsercaoPatrocinadorForaRedeException()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $root = new Cliente();
        $gerenciador->insereRoot($root);
        
        $cliente1 = new Cliente();
        $gerenciador->insereRede($cliente1, $root);
        
        $cliente2 = new Cliente();
        $cliente1->save($con);
        
        $gerenciador->insereRede($cliente1, $cliente2);
    }
    
    public function testInsercao()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
                
        $root = new Cliente();
        $gerenciador->insereRoot($root);
        
        $cliente1 = new Cliente();
        $gerenciador->insereRede($cliente1, $root);
        
        $cliente2 = new Cliente();
        $gerenciador->insereRede($cliente2, $root);
        
        $cliente3 = new Cliente();
        $gerenciador->insereRede($cliente3, $cliente1);
        
        ClientePeer::clearInstancePool();
        
        $childrenRoot = $root->getChildren(null, $con);
        $this->assertEquals(2, count($childrenRoot));
        $this->assertEquals($cliente1->getId(), $childrenRoot[0]->getId());
        $this->assertEquals($cliente2->getId(), $childrenRoot[1]->getId());
        
        $this->assertEquals($cliente1->getId(), $cliente3->getParent($con)->getId());
        $this->assertEquals($cliente1->getId(), $cliente3->getClienteIndicadorId());
    }
    
    /**
     * Verifica que ao inserir o cliente em uma arvore vazia, tranforma o cliente no root da arvore.
     */
    public function testInsercaoArvoreVazia()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $cliente = new Cliente();
        $cliente->save();
        
        $gerenciador->insereRede($cliente);
        
        $this->assertEquals($cliente->getId(), ClienteQuery::create()->findRoot()->getId());
    }
    
    
    /**
     * Testa o caso em que a rede possui apenas o root, e o segundo, terceiro e quarto clientes não informam seu patrocinador.
     */
    public function testInsercaoArvoreApenasRoot()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
                
        $root = new Cliente();
        $root->setNome('root');
        $gerenciador->insereRoot($root);
                
        $cliente1 = new Cliente();
        $cliente1->setNome('cliente1');
        
        $patrocinador = $gerenciador->insereRede($cliente1);
        
        $this->assertEquals('root', $patrocinador->getNome());
        $this->assertEquals('root', $cliente1->getParent()->getNome());
        $this->assertEquals('root', $cliente1->getClienteRelatedByClienteIndicadorId()->getNome());
        
        $cliente2 = new Cliente();
        $cliente2->setNome('cliente2');
        
        $patrocinador = $gerenciador->insereRede($cliente2);
        
        $this->assertEquals('root', $patrocinador->getNome());
        $this->assertEquals('root', $cliente2->getParent()->getNome());
        $this->assertEquals('root', $cliente2->getClienteRelatedByClienteIndicadorId()->getNome());
        
        
        $cliente3 = new Cliente();
        $cliente3->setNome('cliente3');
        
        $patrocinador = $gerenciador->insereRede($cliente3);
        
        $this->assertEquals('cliente1', $patrocinador->getNome());
        $this->assertEquals('cliente1', $cliente3->getParent()->getNome());
        $this->assertEquals('cliente1', $cliente3->getClienteRelatedByClienteIndicadorId()->getNome());
    }
    
    
    /**
     * Verifica que ao inserir um cliente na rede sem definir o patrocinador, o gerenciador escolhe um patrocinador disponivel corretamente.
     */
    public function testInsercaoSemPatrocinadorDefinido()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinador = $gerenciador->insereRede($novo);
        
        //O primeiro patrocinador disponivel na arvore de testes é o "2.3"
        $this->assertEquals('2.3', $patrocinador->getNome());
        
        ClientePeer::clearInstancePool();
        
        $cliente = ClienteQuery::create()->findOneByNome($nome);
        $this->assertEquals('2.3', $cliente->getClienteRelatedByClienteIndicadorId()->getNome());
        $this->assertEquals('2.3', $cliente->getParent()->getNome());
    }
    
    
    public function testInsercaoPatrocinadorDefinido()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $patrocinador = ClienteQuery::create()->findOneByNome('3.3');
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorRet = $gerenciador->insereRede($novo, $patrocinador);
        
        $this->assertEquals($patrocinador->getId(), $patrocinadorRet->getId());
        
        ClientePeer::clearInstancePool();
        
        $cliente = ClienteQuery::create()->findOneByNome($nome);
        $this->assertEquals('3.3', $cliente->getClienteRelatedByClienteIndicadorId()->getNome());
        $this->assertEquals('3.3', $cliente->getParent()->getNome());
        
        $vizinhos = $cliente->getSiblings();
        $this->assertEquals('4.1', $vizinhos[0]->getNome());
    }
    
    /**
     * Verifica que ao inserir um cliente na rede escolhendo como patrocinador um patrocinador não disponível,
     * o gerenciador escolhe um outro patrocinador disponivel corretamente.
     *
     */
    public function testInsercaoComPatrocinadorDefinidoNaoDisponivel()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $patrocinador = ClienteQuery::create()->findOneByNome('1.1');
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorRet = $gerenciador->insereRede($novo, $patrocinador);
        
        //O patrocinador "1.1" já possui dois filhos. O gerenciador deveria escolher neste caso o cliente "3.1" como patrocinador (o primeiro da subarvore sem filhos)
        $this->assertEquals('3.1', $patrocinadorRet->getNome());
        
        //mas deve associar o patrocinador solicitado '1.1' nesta associação (lienteRelatedByClienteIndicadorDiretoId)
        $this->assertEquals('1.1', $novo->getClienteRelatedByClienteIndicadorDiretoId()->getNome());
        
        ClientePeer::clearInstancePool();
        
        $cliente = ClienteQuery::create()->findOneByNome($nome);
        $this->assertEquals('3.1', $cliente->getClienteRelatedByClienteIndicadorId()->getNome());
        $this->assertEquals('3.1', $cliente->getParent()->getNome());
    }
    
    /***
     * Outro teste similiar ao anterior.
     *
     */
    public function testInsercaoComPatrocinadorDefinidoNaoDisponivel2()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $patrocinador = ClienteQuery::create()->findOneByNome('2.2');
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorRet = $gerenciador->insereRede($novo, $patrocinador);
        
        //O patrocinador "2.2" já possui dois filhos. O gerenciador deveria escolher neste caso o cliente "3.4" como patrocinador (o primeiro da subarvore sem filhos)
        $this->assertEquals('3.4', $patrocinadorRet->getNome());
        
        ClientePeer::clearInstancePool();
        
        $cliente = ClienteQuery::create()->findOneByNome($nome);
        $this->assertEquals('3.4', $cliente->getClienteRelatedByClienteIndicadorId()->getNome());
        $this->assertEquals('3.4', $cliente->getParent()->getNome());
    }
    
    
    public function testGeracaoHtml()
    {
        $this->criarArvoreTeste();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $this->assertEquals($gerenciador->geraHTMLRede(ClienteQuery::create()->findRoot()), '<ul id="rede-clientes"><li>root<ul><li>1.1<ul><li>2.1<ul><li>3.1</li><li>3.2</li></ul></li><li>2.2<ul><li>3.3<ul><li>4.1</li></ul></li><li>3.4</li></ul></li></ul></li><li>1.2<ul><li>2.3</li></ul></li><li>1.3<ul><li>2.4<ul><li>3.5<ul><li>4.2</li></ul></li></ul></li></ul></li><li>1.4<ul><li>2.5</li><li>2.6<ul><li>3.6</li></ul></li></ul></li></ul></li></ul>');
    }
    
    public function testGeracaoHtml2()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->insertAsFirstChildOf($root);
        $cliente1->save($con);
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->insertAsLastChildOf($root);
        $cliente2->save($con);
        
        $this->assertEquals($gerenciador->geraHTMLRede($root), '<ul id="rede-clientes"><li>root<ul><li>Cliente 1</li><li>Cliente 2</li></ul></li></ul>');
    }
    
    public function testGeracaoHtml3()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $root = new Cliente();
        $root->setNome('Food:');
        $root->makeRoot();
        $root->save($con);
        
        $beer = new Cliente();
        $beer->setNome('Beer');
        $beer->insertAsFirstChildOf($root);
        $beer->save($con);
        
        $vegetables = new Cliente();
        $vegetables->setNome('Vegetables');
        $vegetables->insertAsLastChildOf($root);
        $vegetables->save($con);
        
        $chocolate = new Cliente();
        $chocolate->setNome('Chocolate');
        $chocolate->insertAsLastChildOf($root);
        $chocolate->save($con);
        
        $carrot = new Cliente();
        $carrot->setNome('Carrot');
        $carrot->insertAsFirstChildOf($vegetables);
        $carrot->save($con);
        
        $pea = new Cliente();
        $pea->setNome('Pea');
        $pea->insertAsLastChildOf($vegetables);
        $pea->save($con);
        
        $this->assertEquals($gerenciador->geraHTMLRede($root, 'org'), '<ul id="org"><li>Food:<ul><li>Beer</li><li>Vegetables<ul><li>Carrot</li><li>Pea</li></ul></li><li>Chocolate</li></ul></li></ul>');
    }
    
    public function testGeracaoHtml4()
    {
        //Teste parecido com testGeracaoHtml2, mas exibindo o nome do plano de cada cliente
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $plano1 = new Plano();
        $plano1->setNome('Plano 1');
        $plano1->save($con);
        
        $plano2 = new Plano();
        $plano2->setNome('Plano 2');
        $plano2->save($con);
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->setPlano($plano1);
        $root->save($con);
        
        $cliente1 = new Cliente();
        $cliente1->setNome('Cliente 1');
        $cliente1->insertAsFirstChildOf($root);
        $cliente1->save($con);
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->insertAsLastChildOf($root);
        $cliente2->setPlano($plano2);
        $cliente2->save($con);
        
        $this->assertEquals($gerenciador->geraHTMLRede($root, 'rede-clientes', true), '<ul id="rede-clientes"><li>root<br><small>Plano 1</small><ul><li>Cliente 1</li><li>Cliente 2<br><small>Plano 2</small></li></ul></li></ul>');
    }
    
    public function testaGeracaoHtmlApenasRoot()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
        
        $this->assertEquals($gerenciador->geraHTMLRede($root), '<ul id="rede-clientes"><li>root</li></ul>');
    }
    
    public function testaGeracaoHtmlOutroId()
    {
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $root = new Cliente();
        $root->setNome('root');
        $root->makeRoot();
        $root->save($con);
                
        $this->assertEquals($gerenciador->geraHTMLRede($root, 'outro-id'), '<ul id="outro-id"><li>root</li></ul>');
    }
    
    
    public function testInsercaoComLadoinsercaoDefinido()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorSolicitado = ClienteQuery::create()->findOneByNome('root');
        $patrocinadorSolicitado->setLadoInsercaoCadastrados(Cliente::LADO_DIREITO);
        $patrocinadorSolicitado->save($con);
        
        $patrocinador = $gerenciador->insereRede($novo, $patrocinadorSolicitado);
        
        //O primeiro patrocinador disponivel na arvore de testes é o "2.3",
        //mas como $patrocinadorSolicitado solicitou inserções no lado direito, o patrocinador deve ser "2.5"
        //atenção: na nossa arvore de testes, root possui mais que dois filhos. Nesse caso o da direita é considerado o ultimo filho.
        $this->assertEquals('2.5', $patrocinador->getNome());
        
        $patrocinadorSolicitado = ClienteQuery::create()->findOneByNome('2.6');
        
        /* não deve causar nenhum efeito pois "2.6" só possui 1 filho. Ele mesmo deve ser o patrocinador */
        $patrocinadorSolicitado->setLadoInsercaoCadastrados(Cliente::LADO_ESQUERDO);
        $patrocinadorSolicitado->save();
        
        $cliente2 = new Cliente();
        $cliente2->setNome('Cliente 2');
        $cliente2->save($con);
        
        $patrocinador = $gerenciador->insereRede($cliente2, $patrocinadorSolicitado);
        
        $this->assertEquals('2.6', $patrocinador->getNome());
    }
    
    public function testInsercaoComLadoinsercaoDefinido2()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorSolicitado = ClienteQuery::create()->findOneByNome('2.2');
        $patrocinadorSolicitado->setLadoInsercaoCadastrados(Cliente::LADO_ESQUERDO);
        $patrocinadorSolicitado->save($con);
        
        $patrocinador = $gerenciador->insereRede($novo, $patrocinadorSolicitado);
        
        //deveria usar como patrocinador o cliente "3.4", mas como o $patrocinadorSolicitado solicitou inserções a esquerda
        //o patrocinador escolhido deve ser "4.1"
        $this->assertEquals('4.1', $patrocinador->getNome());
    }
    
    public function testInsercaoComLadoinsercaoDefinido3()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorSolicitado = ClienteQuery::create()->findOneByNome('1.4');
        $patrocinadorSolicitado->setLadoInsercaoCadastrados(Cliente::LADO_DIREITO);
        
        $patrocinador = $gerenciador->insereRede($novo, $patrocinadorSolicitado);
        
        //deveria ser "2.5", mas como $patrocinadorSolicitado solicitou inserções a diretira deve ser "3.6"
        $this->assertEquals('3.6', $patrocinador->getNome());
    }
    
    public function testInsercaoComLadoinsercaoDefinido4()
    {
        $this->criarArvoreTeste();
        
        ClientePeer::clearInstancePool();
        
        $gerenciador = new GerenciadorRede($con = Propel::getConnection());
        
        $novo = new Cliente();
        $novo->setNome($nome = 'Novo cliente');
        
        $patrocinadorSolicitado = ClienteQuery::create()->findOneByNome('2.1');
        $patrocinadorSolicitado->setLadoInsercaoCadastrados(Cliente::LADO_DIREITO);
        
        $patrocinador = $gerenciador->insereRede($novo, $patrocinadorSolicitado);
        
        //deveria ser "3.1", mas como solicitou inserções a direita deve ser "3.2"
        $this->assertEquals('3.2', $patrocinador->getNome());
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
    protected function criarArvoreTeste()
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

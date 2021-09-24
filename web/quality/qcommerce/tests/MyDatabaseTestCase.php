<?php

/**
 * Description of MyDatabaseTestCase
 *
 * @author André Garlini
 */
abstract class MyDatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    private static $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO('mysql:dbname=oxi3-qcommerce-tests;host=localhost', 'root', '');
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'mysql');
        }
        
        //IMPORTANTE: precisamos limpar todos os pools usados, senão pode ocorrer conflito com testes anteriores.
        ClientePeer::clearInstancePool();
        CategoriaPeer::clearInstancePool();
        PlanoPeer::clearInstancePool();
        ExtratoPeer::clearInstancePool();
        ResgatePeer::clearInstancePool();
        ProdutoPeer::clearInstancePool();
        ProdutoVariacaoPeer::clearInstancePool();
        PedidoPeer::clearInstancePool();
        PedidoItemPeer::clearInstancePool();
        ProdutoPeer::clearRelatedInstancePool();
        PedidoPeer::clearRelatedInstancePool();
        DistribuicaoPeer::clearInstancePool();
        DistribuicaoClientePeer::clearInstancePool();
        ParticipacaoResultadoPeer::clearInstancePool();
        ParticipacaoResultadoClientePeer::clearInstancePool();
        PedidoFormaPagamentoPeer::clearInstancePool();
        PedidoStatusHistoricoPeer::clearInstancePool();
        PlanoCarreiraClientePeer::clearInstancePool();
        
        return $this->conn;
    }
}

<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_cliente_distribuidor' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ClienteDistribuidorQuery extends BaseClienteDistribuidorQuery
{
    /**
     *
     * @param string $alias
     * @return \ClienteDistribuidorQuery
     */
    public function addNomeCompletoColumn($alias = 'nome_completo')
    {
        $clause = "TRIM(CONCAT(COALESCE(:nome, ''), ' ', COALESCE(:sobrenome, '')))";

        $clause = strtr($clause, array(
            ':nome' => ClienteDistribuidorPeer::NOME_RAZAO_SOCIAL,
            ':sobrenome' => ClienteDistribuidorPeer::SOBRENOME_NOME_FANTASIA
        ));

        $this->withColumn($clause, $alias);

        return $this;
    }

    /**
     *
     * @param string $alias
     * @return \ClienteDistribuidorQuery
     */
    public function addUltimaCompraColumn($alias = 'ultima_compra')
    {
        $clause = "(SELECT :pedido.created_at FROM :pedido"
            . " INNER JOIN :historico ON :pedido.id = :historico.pedido_id"
            . " INNER JOIN :situacao ON :historico.situacao_id = :situacao.id"
            . " INNER JOIN :cliente ON :pedido.cliente_id = :cliente.id"
            . " INNER JOIN :cliente_distribuidor ON :cliente_distribuidor.cliente_id = :cliente.id"
            . " WHERE :pedido.status <> 'CANCELADO'"
            . " AND :situacao.ordem >= 2"
            . " AND :cliente.email = :cliente_distribuidor.email"
            . " ORDER BY :pedido.created_at DESC"
            . " LIMIT 1)";

        $clause = strtr($clause, array(
            ':pedido.created_at' => PedidoPeer::CREATED_AT,
            ':pedido' => PedidoPeer::TABLE_NAME,
            ':historico' => HistoricoPeer::TABLE_NAME,
            ':pedido.id' => PedidoPeer::ID,
            ':historico.pedido_id' => HistoricoPeer::PEDIDO_ID,
            ':situacao' => SituacaoPeer::TABLE_NAME,
            ':historico.situacao_id' => HistoricoPeer::SITUACAO_ID,
            ':situacao.id' => SituacaoPeer::ID,
            ':cliente' => ClientePeer::TABLE_NAME,
            ':pedido.cliente_id' => PedidoPeer::CLIENTE_ID,
            ':cliente.id' => ClientePeer::ID,
            ':pedido.status' => PedidoPeer::STATUS,
            ':situacao.ordem' => SituacaoPeer::ORDEM,
            ':cliente.email' => ClientePeer::EMAIL,
            ':cliente_distribuidor.email' => ClienteDistribuidorPeer::EMAIL
        ));

        $this->withColumn($clause, $alias);
        return $this;
    }

    /**
     *
     * @param Cliente $cliente
     * @param DateTime $inicio
     * @param Datetime $fim
     * @return float
     */
    public static function getValorVendasPeriodo(Cliente $cliente, DateTime $inicio = null, Datetime $fim = null)
    {
        $subqueryEmailsDistribuidor = ''
            . 'SELECT [cliente_distribuidor.email] FROM [cliente_distribuidor]'
            . ' WHERE [cliente_distribuidor.cliente_id] = :cliente_id'
            . '';

        $subqueryEmailsDistribuidor = strtr($subqueryEmailsDistribuidor, array(
            '[cliente_distribuidor.email]' => ClienteDistribuidorPeer::EMAIL,
            '[cliente_distribuidor]' => ClienteDistribuidorPeer::TABLE_NAME,
            '[cliente_distribuidor.cliente_id]' => ClienteDistribuidorPeer::CLIENTE_ID
        ));


        $subqueryPedidosPagos = 'SELECT DISTINCT [pedido.id]' /* é preciso do distinct pois o join com situacoes pode retornar mais de 1 pedido */
            . ' FROM [pedido]'
            . ' INNER JOIN [cliente] ON [pedido.cliente_id] = [cliente.id]'
            . ' INNER JOIN [historico] ON [historico.pedido_id] = [pedido.id]'
            . ' INNER JOIN [situacao] ON [historico.situacao_id] = [situacao.id]'
            . ' WHERE [pedido.status] <> :pedido_cancelado'
            . ' AND [situacao.ordem] >= 2'
            . " AND [cliente.email] in ($subqueryEmailsDistribuidor)";

        if ($inicio) {
            $subqueryPedidosPagos .= ' AND [pedido.data] >= :inicio';
        }

        if ($fim) {
            $subqueryPedidosPagos .= ' AND [pedido.data] <= :fim';
        }

        $subqueryPedidosPagos = strtr($subqueryPedidosPagos, array(
            '[pedido.valor_total]' => PedidoPeer::VALOR_ITENS,
            '[pedido]' => PedidoPeer::TABLE_NAME,
            '[cliente]' => ClientePeer::TABLE_NAME,
            '[pedido.cliente_id]' => PedidoPeer::CLIENTE_ID,
            '[cliente.id]' => ClientePeer::ID,
            '[historico]' => HistoricoPeer::TABLE_NAME,
            '[historico.pedido_id]' => HistoricoPeer::PEDIDO_ID,
            '[pedido.id]' => PedidoPeer::ID,
            '[situacao]' => SituacaoPeer::TABLE_NAME,
            '[historico.situacao_id]' => HistoricoPeer::SITUACAO_ID,
            '[situacao.id]' => SituacaoPeer::ID,
            '[pedido.status]' => PedidoPeer::STATUS,
            '[situacao.ordem]' => SituacaoPeer::ORDEM,
            '[cliente.email]' => ClientePeer::EMAIL,
            '[pedido.data]' => PedidoPeer::CREATED_AT
        ));
        $parameters = array(
            ':cliente_id' => $cliente->getId(),
            ':pedido_cancelado' => Pedido::CANCELADO
        );
        if ($inicio) {
            $parameters[':inicio'] = $inicio->format('Y-m-d H:i:s');
        }
        if ($fim) {
            $parameters[':fim'] = $fim->format('Y-m-d H:i:s');
        }
//                var_dump($parameters);die;


        $queryValorVendas = "SELECT SUM([pedido.valor]) FROM [pedido] WHERE [pedido.id] in ($subqueryPedidosPagos)";

        $queryValorVendas = strtr($queryValorVendas, array(
            '[pedido.valor]' => PedidoPeer::VALOR_ITENS,
            '[pedido]' => PedidoPeer::TABLE_NAME,
            '[pedido.id]' => PedidoPeer::ID
        ));

        $con = Propel::getConnection(ClienteDistribuidorPeer::DATABASE_NAME);

        $sttm = $con->prepare($queryValorVendas);

        $sttm->execute($parameters);

        if ($row = $sttm->fetch()) {
            return (float)$row[0];
        }

        return 0.0;
    }

    /**
     *
     * @param DateTime|null $mes
     * @return \ClienteDistribuidorQuery
     */
    public function filterByAniversariantesMes(DateTime $mes = null)
    {
        if (null === $mes) {
            $mes = new DateTime();
        }

        $this->add('mes_aniversario', sprintf("DATE_FORMAT(%s, '%%m') = %d", ClienteDistribuidorPeer::DATA_NASCIMENTO_DATA_FUNDACAO, $mes->format('m')), Criteria::CUSTOM);

        return $this;
    }
}

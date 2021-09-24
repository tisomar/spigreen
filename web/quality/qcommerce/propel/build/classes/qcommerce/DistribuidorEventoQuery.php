<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_distribuidor_evento' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuidorEventoQuery extends BaseDistribuidorEventoQuery
{
    /**
     *
     * @param Cliente $cliente
     * @return int
     */
    public static function countEventosAtrasadosDistribuidor(Cliente $cliente)
    {
        $now = new DateTime();

        $query = self::create()
            ->filterByCliente($cliente)
            ->filterByData($now, Criteria::LESS_THAN)
            ->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO);


        return (int)$query->count();
    }

    /**
     *
     * @param Cliente $cliente
     * @return int
     */
    public static function countEventosAlarmadosDistribuidor(Cliente $cliente)
    {
        $configuracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor($cliente);
        $diasAlarme = $configuracao->getDiasAlertaEventosAtrasados();

        if ($diasAlarme > 0) {
            $dataAlarme = new DateTime("-$diasAlarme days");
        } else {
            $dataAlarme = new DateTime();
        }

        $query = self::create()
            ->filterByCliente($cliente)
            ->filterByData($dataAlarme, Criteria::LESS_THAN)
            ->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO);


        return (int)$query->count();
    }


    /**
     *
     * @param Cliente $cliente
     * @return float
     */
    public static function getPotencialVendaEventosAtrasados(Cliente $cliente)
    {
        $data = new Datetime('now');

        return self::getPotencialVendaEventosAnterioresData($cliente, $data);
    }

    public static function getPotencialVendaEventosAlarmados(Cliente $cliente)
    {
        $configuracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor($cliente);
        $diasAlarme = $configuracao->getDiasAlertaEventosAtrasados();

        $dataAlarme = new DateTime("-$diasAlarme days");

        return self::getPotencialVendaEventosAnterioresData($cliente, $dataAlarme);
    }

    /**
     *
     * @param Cliente $cliente
     * @param DateTime $data
     * @return float
     */
    public static function getPotencialVendaEventosAnterioresData(Cliente $cliente, DateTime $data)
    {
        $subqueryEmailsEventosAtrasados = '('
            . 'SELECT DISTINCT [cliente_distribuidor.email] FROM [evento]'
            . ' INNER JOIN [cliente_distribuidor] ON [evento.cliente_distribuidor_id] = [cliente_distribuidor.id]'
            . ' WHERE [evento.cliente_id] = :cliente_id'
            . ' AND [evento.data] < :data'
            . ' AND [evento.status] = :status_andamento'
            . ')';

        $subqueryEmailsEventosAtrasados = strtr($subqueryEmailsEventosAtrasados, array(
            '[cliente_distribuidor.email]' => ClienteDistribuidorPeer::EMAIL,
            '[evento]' => DistribuidorEventoPeer::TABLE_NAME,
            '[cliente_distribuidor]' => ClienteDistribuidorPeer::TABLE_NAME,
            '[evento.cliente_distribuidor_id]' => DistribuidorEventoPeer::CLIENTE_DISTRIBUIDOR_ID,
            '[cliente_distribuidor.id]' => ClienteDistribuidorPeer::ID,
            '[evento.cliente_id]' => DistribuidorEventoPeer::CLIENTE_ID,
            '[evento.data]' => DistribuidorEventoPeer::DATA,
            '[evento.status]' => DistribuidorEventoPeer::STATUS
        ));


        $subqueryValorUltimaCompraCliente = '('
            . 'SELECT [pedido.valor_total] FROM [pedido]'
            . ' INNER JOIN [historico] ON [historico.pedido_id] = [pedido.id]'
            . ' INNER JOIN [situacao] ON [historico.situacao_id] = [situacao.id]'
            . ' WHERE [pedido.cliente_id] = [cliente.id]'
            . ' AND [pedido.situacao] <> :pedido_cancelado'
            . ' AND [situacao.ordem] >= 2'
            . ' ORDER BY [pedido.data] DESC'
            . ' LIMIT 1'
            . ')';

        $subqueryValorUltimaCompraCliente = strtr($subqueryValorUltimaCompraCliente, array(
            '[pedido.valor_total]' => PedidoPeer::VALOR_TOTAL,
            '[pedido]' => PedidoPeer::TABLE_NAME,
            '[historico]' => HistoricoPeer::TABLE_NAME,
            '[historico.pedido_id]' => HistoricoPeer::PEDIDO_ID,
            '[pedido.id]' => PedidoPeer::ID,
            '[situacao]' => SituacaoPeer::TABLE_NAME,
            '[historico.situacao_id]' => HistoricoPeer::SITUACAO_ID,
            '[situacao.id]' => SituacaoPeer::ID,
            '[pedido.cliente_id]' => PedidoPeer::CLIENTE_ID,
            '[cliente.id]' => ClientePeer::ID,
            '[pedido.situacao]' => PedidoPeer::SITUACAO,
            '[situacao.ordem]' => SituacaoPeer::ORDEM,
            '[pedido.data]' => PedidoPeer::DATA
        ));


        $strQuery = "SELECT SUM($subqueryValorUltimaCompraCliente)"
            . " FROM [cliente]"
            . " WHERE [cliente.email] IN ($subqueryEmailsEventosAtrasados)";

        $strQuery = strtr($strQuery, array(
            '[cliente]' => ClientePeer::TABLE_NAME,
            '[cliente.email]' => ClientePeer::EMAIL
        ));

        $con = Propel::getConnection(DistribuidorEventoPeer::DATABASE_NAME);

        $sttm = $con->prepare($strQuery);

        $sttm->execute(array(
            ':cliente_id' => $cliente->getId(),
            ':data' => $data->format('Y-m-d H:i:s'),
            ':status_andamento' => DistribuidorEvento::STATUS_ANDAMENTO,
            ':pedido_cancelado' => Pedido::CANCELADO
        ));

        if ($row = $sttm->fetch()) {
            return (float)$row[0];
        }

        return 0.0;
    }

    /**
     *
     * @param Cliente $cliente
     * @param DateTime $data
     * @return PropelObjectCollection|array
     */
    public static function findEventosAbertosDistribuidorNaData(Cliente $cliente, DateTime $data)
    {
        $inicio = clone $data;
        $inicio->setTime(0, 0, 0);

        $fim = clone $data;
        $fim->setTime(23, 59, 59);

        $query = self::create()
            ->filterByCliente($cliente)
            ->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO)
            ->filterByData($inicio, Criteria::GREATER_EQUAL)
            ->filterByData($fim, Criteria::LESS_EQUAL);

        return $query->find();
    }
}

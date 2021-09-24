<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

$con = null;

try {
    $con = Propel::getConnection(ProdutoVendaEstatisticaPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);

    $countParameter = Config::get('sistema.cadastro_vago') ?? 0;
    $mesesParameter = Config::get('sistema.qtd_meses_inativacao');
    $mesesParameterPreferencial = Config::get('sistema.qtd_meses_inativacao_preferencial');
    $clientesAtivacaoPermanente = ClientePeer::getClientesAtivosPermanente();

    $minDate = new Datetime("first day of -{$mesesParameter} months");
    $minDate->setTime(0, 0);

    $clientes = ClienteQuery::create()
        ->filterByDataAtivacao($minDate, Criteria::LESS_THAN)
        ->filterByVago(0)
        ->filterByStatus(1)
        ->filterById($clientesAtivacaoPermanente, Criteria::NOT_IN)
        ->usePlanoQuery()
            ->filterByPlanoClientePreferencial(false)
        ->endUse();

    $con->beginTransaction();

    $con->useDebug(true);

    foreach ($clientes->find() as $cliente) :
        $pedidos = PedidoQuery::create()
                ->filterByPagamentoConfirmado()
                ->select(['result'])
                ->withColumn('1', 'result')
                ->filterByClienteId($cliente->getId())
                ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                ->filterByCreatedAt(['min' => $minDate])
                ->addGroupByColumn(sprintf('MONTH(%s)', PedidoPeer::CREATED_AT))
                ->having(
                    sprintf('SUM(%s) >= ?', PedidoPeer::VALOR_PONTOS),
                    ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal(),
                    PDO::PARAM_INT
                );

        if ($pedidos->count() == 0) :
            $cliente->zerarCadastro(++$countParameter);
        endif;
    endforeach;

    $minDate = new Datetime("first day of -{$mesesParameterPreferencial} months");
    $minDate->setTime(0, 0);

    $clientesPreferenciais = ClienteQuery::create()
        ->filterByDataAtivacao($minDate, Criteria::LESS_THAN)
        ->filterByVago(0)
        ->filterByStatus(1)
        ->filterById($clientesAtivacaoPermanente, Criteria::NOT_IN)
        ->usePlanoQuery()
            ->filterByPlanoClientePreferencial(true)
        ->endUse();

    foreach ($clientesPreferenciais->find() as $cliente) :
        $pedido = $cliente->getUltimoPedidoConfirmado();

        if (!$pedido || $pedido->getCreatedAt(null) < $minDate) :
            $cliente->zerarCadastro(++$countParameter);
        endif;
    endforeach;

    Config::saveParameter('sistema.cadastro_vago', $countParameter);

    $con->commit();

    echo 'Concluído com sucesso.';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();

    if (!empty($con) && $con->inTransaction()) :
        $con->rollBack();
    endif;
}

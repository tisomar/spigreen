<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

try {
    $sql = "qp1_pedido.Id NOT IN (select PEDIDO_ID from qp1_extrato where qp1_extrato.CLIENTE_ID = qp1_pedido.CLIENTE_ID and qp1_extrato.TIPO in ('INDICACAO_DIRETA', 'RESIDUAL'))";

    $criteria = new Criteria();

    $criteria->add(PedidoPeer::ID, $sql, Criteria::CUSTOM);

    $pedidosSemExtrato = PedidoPeer::doSelect($criteria);

    $con = Propel::getConnection(PedidoPeer::DATABASE_NAME);

    // Serão criados extratos para cada pedido que não possui extrato com o mesmo cliente
    // do tipo indicação direta e recompra (extratos para pontuação pessoal)
    /** @var $item Pedido */
    foreach ($pedidosSemExtrato as $pedido) :
        $criarExtratoDireta = false;
        $criarExtratoRecompra = false;

        // Cria o extrato caso o status do pedido não seja cancelado ou
        // o pagamento ainda não estiver confirmado
        if ($pedido->getStatus() !== PedidoPeer::STATUS_CANCELADO) :
            if ($pedido->getLastPedidoStatus() && $pedido->getLastPedidoStatus()->getId() != 1) :
                foreach ($pedido->getPedidoItems(null, $con) as $pedidoItem) :
                    if ($pedidoItem->getProdutoVariacao()->getProduto()->isKitAdesao()) :
                        $criarExtratoDireta = true;
                    endif;

                    /*
                     * Verifica se há item no pedido que não seja kit, para criar o extrato de recompra
                     */
                    if (!in_array($pedidoItem->getProdutoVariacao()->getProduto()->getId(), [2, 123])) :
                        $criarExtratoRecompra = true;
                    endif;
                endforeach;
            endif;
        endif;

        if ($criarExtratoDireta) :
            $extrato = new Extrato();
            $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
            $extrato->setOperacao('+');
            $extrato->setPontos(0);
            $extrato->setCliente($pedido->getCliente());
            $extrato->setPedido($pedido);
            $extrato->setData($pedido->getCreatedAt());
            $extrato->setObservacao('Pontos de ativação pessoal. Pedido '. $pedido->getId());
            $extrato->save($con);

            echo "Extrato direta cadastrado para o pedido " . $pedido->getId() . "<br>";
        endif;

        if ($criarExtratoRecompra) :
            $extrato = new Extrato();
            $extrato->setTipo(Extrato::TIPO_RESIDUAL);
            $extrato->setOperacao('+');
            $extrato->setPontos(0);
            $extrato->setCliente($pedido->getCliente());
            $extrato->setPedido($pedido);
            $extrato->setData($pedido->getCreatedAt());
            $extrato->setObservacao('Pontos de recompra pessoal. Pedido '. $pedido->getId());
            $extrato->save($con);

            echo "Extrato recompra cadastrado para o pedido " . $pedido->getId() . "<br>";
        endif;
    endforeach;
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\PropelException $pe) {
    $logger->error($pe->getMessage());
} catch (Exception $e) {
    $logger->error($e->getMessage());
}
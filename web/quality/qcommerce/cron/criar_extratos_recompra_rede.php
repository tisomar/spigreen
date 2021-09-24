<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

try {
    // Foi verificado que havia apenas um pedido na qual não havia sido gerado
    // extrato para as pessoas da rede do cleinte que fez o pedido (Patrícia Koh Soh)
    // Serão criados apenas os extratos para este pedido

    $con = Propel::getConnection(PedidoPeer::DATABASE_NAME);

    /**
     * @var $pedido Pedido
     */
    $pedido = PedidoQuery::create()
        ->filterById(2402)
        ->findOne();

    if (!ExtratoQuery::create()->filterByClienteId(141)->filterByPedido($pedido)->findOne()) :
        // Extrato de recompra para Daniele Min Re Soh Son
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setClienteId(141);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Bônus recompra. Pedido '. $pedido->getId() . ' - Cliente ' . $pedido->getCliente()->getNome());
        $extrato->save($con);

        echo 'Extrato do pedido ' . $pedido->getId() . ' - Cliente Daniele Min Re Soh Son criado.';
    endif;

    if (!ExtratoQuery::create()->filterByClienteId(9)->filterByPedido($pedido)->findOne()) :
        // Extrato de recompra para JC PARTNERS
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setClienteId(9);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Bônus recompra. Pedido '. $pedido->getId() . ' - Cliente ' . $pedido->getCliente()->getNome());
        $extrato->save($con);

        echo "<br>";
        echo 'Extrato do pedido ' . $pedido->getId() . ' - Cliente JC PARTNERS criado.';
    endif;

    if (!ExtratoQuery::create()->filterByClienteId(7)->filterByPedido($pedido)->findOne()) :
        // Extrato de recompra para INSTITUTO LIVRES
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setClienteId(7);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Bônus recompra. Pedido '. $pedido->getId() . ' - Cliente ' . $pedido->getCliente()->getNome());
        $extrato->save($con);

        echo "<br>";
        echo 'Extrato do pedido ' . $pedido->getId() . ' - Cliente INSTITUTO LIVRES criado.';
    endif;

    if (!ExtratoQuery::create()->filterByClienteId(3)->filterByPedido($pedido)->findOne()) :
        // Extrato de recompra para Reinaldo Gomes de Morais
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setClienteId(3);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Bônus recompra. Pedido '. $pedido->getId() . ' - Cliente ' . $pedido->getCliente()->getNome());
        $extrato->save($con);

        echo "<br>";
        echo 'Extrato do pedido ' . $pedido->getId() . ' - Cliente Reinaldo Gomes de Morais criado.';
    endif;

    if (!ExtratoQuery::create()->filterByClienteId(1)->filterByPedido($pedido)->findOne()) :
        // Extrato de recompra para GEODESIC CENTER HOUSE OF PRAYER OF SOUTH AMERICA
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setClienteId(1);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Bônus recompra. Pedido '. $pedido->getId() . ' - Cliente ' . $pedido->getCliente()->getNome());
        $extrato->save($con);

        echo "<br>";
        echo 'Extrato do pedido ' . $pedido->getId() . ' - Cliente GEODESIC CENTER HOUSE OF PRAYER OF SOUTH AMERICA criado.';
    endif;
} catch (\PDOException $pe) {
    $logger->error($pe->getMessage());
} catch (\PropelException $pe) {
    $logger->error($pe->getMessage());
} catch (Exception $e) {
    $logger->error($e->getMessage());
}
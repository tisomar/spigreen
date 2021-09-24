<?php

namespace App\DB;

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
use PedidoItemQuery;
use PedidoPeer;
use PedidoQuery;
use Plano;
use Produto;
use Propel;
use PropelObjectCollection;

/**
 * Class ConsertarExtrato
 *
 * @package App\DB
 * @author Jesse Quinn
 */
class ConsertarExtrato
{
    /**
     * Find all order items that are kits.
     *
     * @return array|mixed|PropelObjectCollection
     */
    public function mostrarPedidoItemKits()
    {
        return PedidoItemQuery::create()
            ->filterByValorUnitario(881.75) // kit value
            ->find();
    }

    /**
     * @param PropelObjectCollection $pedidoItens
     */
    public function mostrarPedidoKits(PropelObjectCollection $pedidoItens)
    {
        foreach ($pedidoItens as $pedidoItem) :
            /** var PedidoItem $pedidoItem */
            echo $pedidoItem->getPedidoId() . "\n";
        endforeach;
    }

    /**
     * @param PropelObjectCollection $pedidoItens
     * @throws \PropelException
     * @throws \Exception
     */
    public function gerarBonificacao(PropelObjectCollection $pedidoItens): void
    {
        $logger = new Logger('test-channel');
        $logger->pushHandler(new StreamHandler('tests/debug_app.log', Logger::DEBUG));
        $gerenciadorPontos =
            new GerenciadorPontos($con = Propel::getConnection(), $logger);

        foreach ($pedidoItens as $pedidoItem) :
            /** var PedidoItem $pedidoItem */
            $pedido = PedidoQuery::create()
                ->findPk($pedidoItem->getPedidoId());

            if ($pedido->getStatus() == Pedido::FINALIZADO) :
                $gerenciadorPontos->distribuiPontosPedido($pedido);
                echo "Status: " . $pedido->getStatus() . " Pedido: " . $pedidoItem->getPedidoId() . "\n";
            else :
                echo "Status: " . $pedido->getStatus() . " Pedido: " . $pedidoItem->getPedidoId() . "\n";
            endif;
        endforeach;
    }
}

$consertarExtrato = new ConsertarExtrato();
//$consertarExtrato->mostrarPedidoKits($consertarExtrato->mostrarPedidoItemKits());
try {
    $consertarExtrato->gerarBonificacao($consertarExtrato->mostrarPedidoItemKits());
} catch (\PropelException $pe) {
    echo $pe->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}

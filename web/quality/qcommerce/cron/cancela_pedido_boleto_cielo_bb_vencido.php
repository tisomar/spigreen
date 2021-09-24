<?php

/**
 * Esta rotina seleciona os pedidos que possuem forma de pagamento por boleto
 * que estão vencidos e envia um e-mail ao administrador da loja.
 *
 * Esta rotina pode ser executada 1x ao dia entre às 3h. - * 0 3 * * * /path_to_script
 */

set_time_limit(0);
ini_set('memory_limit', '-1');

$inicio = time();
$con = Propel::getConnection();

$nl = ('cli' === php_sapi_name()) ? "\n" : "<br>";

include __DIR__ . '/../includes/include_config.inc.php';

$diasParaVencimento = Config::get('cielo-boleto.quantidade_dias_vencimento');

$pedidosBoletoVencido = PedidoFormaPagamentoQuery::create()
        ->joinWith('PedidoFormaPagamento.Pedido')
        ->joinWith('Pedido.Cliente')
        ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
        ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB)
        ->filterByDataVencimento(date('Y-m-d', strtotime(sprintf("-%d day", $diasParaVencimento))), Criteria::LESS_THAN)
        ->find();

foreach($pedidosBoletoVencido as $pedPagemento) :
    $pedidoId = $pedPagemento->getPedidoId();

    try {
        
        $pedidoCancelar = PedidoQuery::create()->filterById($pedidoId)->findOne();

        if($pedidoCancelar->getLastPedidoStatus() !== null) :
            $pedidoItems = $pedidoCancelar->getPedidoItems();
            $cliente = $pedidoCancelar->getCliente();

            foreach ($pedidoItems as $pi) :

                $saidaEstque = EstoqueProdutoQuery::create()
                    ->filterByPedidoId($pedidoId)
                    ->filterByProdutoVariacaoId($pi->getProdutoVariacaoId())
                    ->findOne();

                if (!is_null($saidaEstque)) {
                    $saidaEstque->delete($con);
                }

                $pi->getProdutoVariacao()->aumentarEstoque($pi->getQuantidade());
                $pgtoConfirmed = $pedidoCancelar->getLastPedidoStatus()->getId() > 1 && $pedidoCancelar->getLastPedidoStatus()->getId() < 6;

                if ($pgtoConfirmed && !is_null($pi->getPlanoId())) {
                    /** @var $cliente Cliente */
                    $cliente->setPlanoId(null);
                    $cliente->save();
                }
            endforeach;

            $pedidoCancelar->setStatus(PedidoPeer::STATUS_CANCELADO);
            $pedidoCancelar->save();

            $pedPagemento->setStatus(PedidoPeer::STATUS_CANCELADO);
            $pedPagemento->save();

            // Aplica estorno no extrato caso o pedido tenha sido pago com bônus
            $arrExtratos = ExtratoQuery::create()->filterByPedidoId($pedidoId)->find();

            $pagamentosBonus = [
                Extrato::TIPO_PAGAMENTO_PEDIDO,
                Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO,
                Extrato::TIPO_BONUS_FRETE
            ];

            foreach ($arrExtratos as $extrato) {
                /** @var $extrato Extrato */

                if ($extrato->getOperacao() == '+'
                    && !in_array($extrato->getTipo(), $pagamentosBonus)) {
                    $extrato->delete();
                }

                if ($extrato->getOperacao() == '-' && (in_array($extrato->getTipo(), $pagamentosBonus))) {
                    $tipoBonus = in_array($extrato->getTipo(), [
                            Extrato::TIPO_PAGAMENTO_PEDIDO,
                            Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO
                        ])
                        ? 'bônus'
                        : 'bônus frete';

                    $observacaoEstorno = "Estorno de {$tipoBonus} pelo cancelamento do pedido {$pedidoId}.";

                    $extratoEstorno = $extrato->copy();
                    $extratoEstorno->setOperacao('+');
                    $extratoEstorno->setObservacao($observacaoEstorno);
                    $extratoEstorno->setData(new Datetime());
                    $extratoEstorno->save();
                }
            }

            // Aplica o estorno no extrato de cliente preferencial caso o pedido
            // tenha sido pago com pontos
            $arrExtratos = ExtratoClientePreferencialQuery::create()
                ->filterByPedido($pedidoCancelar)
                ->filterByOperacao('+')
                ->find();

            foreach ($arrExtratos as $extratoPreferencial) :
                /** @var $extratoPreferencial ExtratoClientePreferencial */

                $gerenciador = new GerenciadorPontosClientePreferencial();

                $gerenciador->criarExtrato(
                    $pedidoCancelar,
                    new DateTime(),
                    '-',
                    $extratoPreferencial->getPontos(),
                    sprintf('Estorno de pontos pelo cancelamento do pedido %s', $pedidoId)
                );
            endforeach;
        endif;
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }

endforeach;

echo 'Finalizado com sucesso.', $nl;
echo 'Tempo: ', number_format(time() - $inicio, 0, '', '.'), ' (s)', $nl;
echo 'memoria: ', number_format(memory_get_peak_usage(), 0, '', '.');

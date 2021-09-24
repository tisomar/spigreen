<?php


/**
 * Skeleton subclass for performing query and update operations on the 'qp1_fluxo_caixa' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class FluxoCaixaPeer extends BaseFluxoCaixaPeer
{

    /**
     * @param Pedido $objPedido
     * @return bool|array
     */

    public static function gerarFluxoCaixa(Pedido $objPedido)
    {
        $retorno = false;
        $fluxo = FluxoCaixaQuery::create()->findByPedidoId($objPedido->getId());

        if ($objPedido instanceof Pedido && count($fluxo) == 0) :
            try {
                $objFormaPagamento = $objPedido->getPedidoFormaPagamento();

                if (!is_null($objFormaPagamento->getNumeroParcelas()) && $objFormaPagamento->getNumeroParcelas() > 1) {
                    $parcelRoot = 1;

                    for ($i = 0; $i < $objFormaPagamento->getNumeroParcelas(); $i++) :
                        $objFluxoCaixa = new FluxoCaixa();
                        $objFluxoCaixa->setPedidoId($objPedido->getId());
                        $objFluxoCaixa->setParcela($i + $parcelRoot);
                        $objFluxoCaixa->setMaxParcela($objFormaPagamento->getNumeroParcelas());
                        $objFluxoCaixa->setValorParcela(
                            number_format(
                                $objPedido->getValorTotal() /
                                $objFormaPagamento->getNumeroParcelas(),
                                '2',
                                '.',
                                ''
                            )
                        );
                        $objFluxoCaixa->setFormaPagamento($objFormaPagamento->getFormaPagamentoDescricao());
                        $objFluxoCaixa->setDataVencimento(self::gerarDataPagamento($objFormaPagamento, $i));
                        $objFluxoCaixa->setCodigoOcorrencia('P');
                        $objFluxoCaixa->setDiaSemana(self::getDayOfWeek($objFluxoCaixa->getDataVencimento()));
                        $objFluxoCaixa->save();
                    endfor;

                    $retorno = true;
                } else {
                    $objFluxoCaixa = new FluxoCaixa();
                    $objFluxoCaixa->setPedido($objPedido);
                    $objFluxoCaixa->setParcela(1);
                    $objFluxoCaixa->setMaxParcela(1);
                    $objFluxoCaixa->setValorParcela(number_format($objPedido->getValorTotal(), '2', '.', ''));
                    $objFluxoCaixa->setFormaPagamento($objFormaPagamento->getFormaPagamentoDescricao());
                    $objFluxoCaixa->setDataVencimento(self::gerarDataPagamento($objFormaPagamento));
                    $objFluxoCaixa->setCodigoOcorrencia('U');
                    $objFluxoCaixa->setDiaSemana(self::getDayOfWeek($objFluxoCaixa->getDataVencimento()));
                    $objFluxoCaixa->save();

                    $retorno = true;
                }
            } catch (Exception $e) {
                var_dump($e->getMessage());
                die;
                $retorno = false;
            }
        endif;

        return $retorno;
    }

    public static function gerarDataPagamento(PedidoFormaPagamento $objFormaPagamento, $parcel = 0)
    {
        if ($objFormaPagamento instanceof PedidoFormaPagamento) {
            $bandeiras = array(
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO => 'Cartão de Crédito',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO => 'Boleto',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL => 'PayPal',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO => 'PagSeguro',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO => 'Faturamento Direto',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE => 'PagSeguro - Débito Online',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO => 'PagSeguro - Cartão de Crédito',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO => 'PagSeguro - Boleto',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE => 'Itaú Shopline',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS => 'Pontos',
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO => 'Cartão de Crédito',
//                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO => 'Cartão de Débito'
            );

            if (isset($bandeiras[$objFormaPagamento->getFormaPagamento()])) {
                $config = Config::get('contas_receber.dias_' . $objFormaPagamento->getFormaPagamento());
                if (!$config) {
                    return date('Y-m-d');
                } else {
                    $dataAtual = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                    if ($parcel > 0) {
                        $dataAtual->add(new DateInterval('P' . $parcel . 'M'));
                    }

                    if ($config > 0) {
                        $dataAtual->add(new DateInterval('P' . $config . 'D'));
                    }

                    return $dataAtual->format('Y-m-d');
                }
            } else {
                return date('Y-m-d');
            }
        } else {
            return date('Y-m-d');
        }
    }

    public static function getDayOfWeek($date)
    {

        $arrDay = array(
            'tuesday' => 3,
            'sunday' => 1,
            'monday' => 2,
            'wednesday' => 4,
            'thursday' => 5,
            'friday' => 6,
            'saturday' => 7,
        );

        $day = mb_strtolower(date("l", strtotime($date)));

        return isset($arrDay[$day]) ? $arrDay[$day] : 1;
    }

    /**
     * @param FluxoCaixa $objFluxo
     * @return array
     * @throws PropelException
     */
    public static function formatterFluxoCaixaIntegrationBling(FluxoCaixa $objFluxo)
    {


        /** @var Endereco $enderecoEntrega */
        $enderecoEntrega = $objFluxo->getPedido()->getEndereco();

        $cliente = ClientePeer::formatterClienteIntegrationBling($objFluxo->getPedido()->getCliente(), $enderecoEntrega);

        if ($objFluxo->getMaxParcela() > 1) {
            return array(

                'dataEmissao' => $objFluxo->getData('d/m/Y'),
                'vencimentoOriginal' => $objFluxo->getDataVencimento('d/m/Y'),
                'nroDocumento' => resumo($objFluxo->getPedidoId(), '25', ''),
                'valor' => number_format($objFluxo->getValorParcela(), '2', '.', ''),
                'historico' => $objFluxo->getFormaPagamento(),
                'ocorrencia' => array(
                    'ocorrenciaTipo' => $objFluxo->getCodigoOcorrencia(),
                    'nroParcelas' => $objFluxo->getMaxParcela(),
                    'diaVencimento' => $objFluxo->getDataVencimento('d'),
                ),
                'cliente' => $cliente,


            );
        } else {
            return array(

                'dataEmissao' => $objFluxo->getData('d/m/Y'),
                'vencimentoOriginal' => $objFluxo->getDataVencimento('d/m/Y'),
                'nroDocumento' => resumo($objFluxo->getPedidoId(), '25', ''),
                'valor' => number_format($objFluxo->getValorParcela(), '2', '.', ''),
                'historico' => $objFluxo->getFormaPagamento(),
                'ocorrencia' => array(
                    'ocorrenciaTipo' => $objFluxo->getCodigoOcorrencia(),
                ),
                'cliente' => $cliente,

            );
        }
    }
}
